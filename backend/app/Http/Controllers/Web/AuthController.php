<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\User;
use App\Services\SubscriptionPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('user.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->isAdmin() && $user->role !== 'admin') {
                $user->update(['role' => 'admin']);
            }

            return $user->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('user.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function showRegister(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('user.dashboard');
        }

        $selectedPackage = null;
        if ($request->filled('package_id')) {
            $selectedPackage = \App\Models\Package::where('status', 'active')
                ->where('id', $request->input('package_id'))
                ->first();
        }

        return view('auth.register', compact('selectedPackage'));
    }

    public function register(Request $request, \App\Services\SubscriptionPaymentService $paymentService): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
            'package_id' => 'nullable|exists:packages,id',
        ]);

        $freePackage = \App\Models\Package::getDefaultPackage();
        $requestedPackage = !empty($validated['package_id'])
            ? \App\Models\Package::where('status', 'active')->find($validated['package_id'])
            : null;

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role'       => 'user',
            'package_id' => $freePackage->id,
        ]);

        // Auto-generate starter API Key for the new user
        $user->apiKeys()->create([
            'name'      => 'Default API Key',
            'key'       => \App\Models\ApiKey::generateKey(),
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // If user registered for a paid package, immediately initiate checkout & redirect to payment
        if ($requestedPackage && !$requestedPackage->isFree()) {
            try {
                $result = $paymentService->initiateCheckout($user, $requestedPackage);
                return redirect()->away($result['checkout_url']);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Registration Checkout Redirection Error: ' . $e->getMessage(), [
                    'user_id'    => $user->id,
                    'package_id' => $requestedPackage->id,
                ]);

                return redirect()->route('user.upgrade')
                    ->with('warning', 'Akun Anda berhasil dibuat. Terjadi kendala saat menghubungkan ke gateway pembayaran: ' . $e->getMessage() . '. Silakan pilih pembayaran di bawah ini.');
            }
        }

        return redirect()->route('user.dashboard')->with('success', 'Selamat datang! Akun Anda berhasil dibuat.');
    }

    /**
     * Authenticate or register a user using Firebase ID token (Google Sign-In).
     */
    public function firebaseAuth(
        Request $request,
        \App\Services\SubscriptionPaymentService $paymentService
    ): JsonResponse {
        $validated = $request->validate([
            'id_token'   => 'required|string',
            'package_id' => 'nullable|exists:packages,id',
        ]);

        $idToken = $validated['id_token'];
        $firebaseConfig = config('services.firebase');
        $apiKey = $firebaseConfig['api_key'] ?? '';

        $firebaseUser = null;

        // 1. If Firebase Web API key is set, verify via Google Identity Toolkit
        if (!empty($apiKey)) {
            try {
                $response = Http::timeout(10)->post(
                    "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key={$apiKey}",
                    ['idToken' => $idToken]
                );

                if ($response->successful()) {
                    $users = $response->json('users');
                    if (!empty($users[0])) {
                        $u = $users[0];
                        $firebaseUser = [
                            'uid'            => $u['localId'] ?? null,
                            'email'          => $u['email'] ?? null,
                            'displayName'    => $u['displayName'] ?? null,
                            'photoUrl'       => $u['photoUrl'] ?? null,
                            'email_verified' => (bool) ($u['emailVerified'] ?? false),
                        ];
                    }
                } else {
                    Log::warning('Firebase Token Verification Failed via Google Identity Toolkit: ' . $response->body());
                }
            } catch (\Throwable $e) {
                Log::error('Firebase verification connection error: ' . $e->getMessage());
            }
        }

        // 2. Fallback: Parse ID Token JWT Payload directly
        if (!$firebaseUser) {
            $parts = explode('.', $idToken);
            if (count($parts) === 3) {
                $payloadJson = base64_decode(strtr($parts[1], '-_', '+/'));
                $claims = json_decode($payloadJson, true);

                if (is_array($claims) && !empty($claims['sub']) && !empty($claims['email'])) {
                    $firebaseUser = [
                        'uid'            => $claims['user_id'] ?? $claims['sub'],
                        'email'          => $claims['email'],
                        'displayName'    => $claims['name'] ?? null,
                        'photoUrl'       => $claims['picture'] ?? null,
                        'email_verified' => (bool) ($claims['email_verified'] ?? true),
                    ];
                }
            }
        }

        if (!$firebaseUser || empty($firebaseUser['email'])) {
            return response()->json([
                'success' => false,
                'message' => 'Token autentikasi Google tidak valid atau telah kedaluwarsa.',
            ], 422);
        }

        $email = strtolower(trim($firebaseUser['email']));
        $uid = $firebaseUser['uid'] ?? null;
        $displayName = $firebaseUser['displayName'] ?: explode('@', $email)[0];
        $photoUrl = $firebaseUser['photoUrl'] ?? null;

        // 3. Find existing user or register new user
        $hasFirebaseCol = \Illuminate\Support\Facades\Schema::hasColumn('users', 'firebase_uid');
        $hasAvatarCol = \Illuminate\Support\Facades\Schema::hasColumn('users', 'avatar');

        $user = User::where('email', $email)
            ->when($uid && $hasFirebaseCol, fn($q) => $q->orWhere('firebase_uid', $uid))
            ->first();

        if (!$user) {
            $freePackage = Package::getDefaultPackage();
            $userData = [
                'name'              => $displayName,
                'email'             => $email,
                'email_verified_at' => now(),
                'password'          => Hash::make(Str::random(32)),
                'role'              => 'user',
                'package_id'        => $freePackage->id,
            ];
            if ($hasFirebaseCol) {
                $userData['firebase_uid'] = $uid;
            }
            if ($hasAvatarCol) {
                $userData['avatar'] = $photoUrl;
            }

            $user = User::create($userData);

            // Auto-generate starter API Key
            $user->apiKeys()->create([
                'name'      => 'Default API Key',
                'key'       => \App\Models\ApiKey::generateKey(),
                'is_active' => true,
            ]);
        } else {
            $updates = [];
            if ($uid && $hasFirebaseCol && empty($user->firebase_uid)) {
                $updates['firebase_uid'] = $uid;
            }
            if ($photoUrl && $hasAvatarCol && empty($user->avatar)) {
                $updates['avatar'] = $photoUrl;
            }
            if (!empty($updates)) {
                $user->update($updates);
            }
        }

        if ($user->isSuspended()) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda sedang ditangguhkan: ' . ($user->suspend_reason ?? 'Silakan hubungi administrator.'),
            ], 403);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        // 4. If package_id is present and paid, initiate checkout and return checkout_url
        if (!empty($validated['package_id'])) {
            $requestedPackage = Package::where('status', 'active')->find($validated['package_id']);
            if ($requestedPackage && !$requestedPackage->isFree()) {
                try {
                    $result = $paymentService->initiateCheckout($user, $requestedPackage);
                    return response()->json([
                        'success'  => true,
                        'message'  => 'Berhasil masuk! Mengarahkan ke pembayaran paket ' . $requestedPackage->name . '...',
                        'redirect' => $result['checkout_url'],
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Firebase Auth Checkout Error: ' . $e->getMessage(), [
                        'user_id'    => $user->id,
                        'package_id' => $requestedPackage->id,
                    ]);

                    return response()->json([
                        'success'  => true,
                        'message'  => 'Akun berhasil diverifikasi. Mengarahkan ke halaman langganan...',
                        'redirect' => route('user.upgrade'),
                    ]);
                }
            }
        }

        $defaultRedirect = $user->isAdmin() ? route('admin.dashboard') : route('user.dashboard');
        return response()->json([
            'success'  => true,
            'message'  => 'Selamat datang, ' . $user->name . '!',
            'redirect' => $defaultRedirect,
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar.');
    }
}
