<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = User::with(['package'])
            ->withCount(['devices', 'apiKeys'])
            ->withCount(['devices as messages_count' => function ($q) {
                $q->join('messages', 'devices.id', '=', 'messages.device_id');
            }]);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);
        $packages = \App\Models\Package::orderBy('sort_order')->get();

        if ($request->wantsJson() || $request->is('admin/api/*')) {
            return response()->json(['success' => true, 'data' => $users]);
        }

        return view('admin.users.index', compact('users', 'packages'));
    }

    /**
     * Store a newly created user (Admin or Tenant User).
     */
    public function store(StoreUserRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $freePackage = \App\Models\Package::getDefaultPackage();

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role'       => $validated['role'],
            'package_id' => $validated['package_id'] ?? $freePackage->id,
        ]);

        // Auto-generate starter API Key
        $user->apiKeys()->create([
            'name'      => 'Default API Key',
            'key'       => ApiKey::generateKey(),
            'is_active' => true,
        ]);

        if ($request->wantsJson() || $request->is('admin/api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Akun ' . ($user->isAdmin() ? 'Admin (Aplikator)' : 'User (Tenant)') . ' berhasil dibuat!',
                'data'    => $user,
            ], 201);
        }

        return back()->with('success', 'Akun pengguna berhasil dibuat!');
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
        ];

        if (array_key_exists('package_id', $validated)) {
            $data['package_id'] = $validated['package_id'];
        }

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        if ($request->wantsJson() || $request->is('admin/api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Data akun berhasil diperbarui!',
                'data'    => $user->fresh(),
            ]);
        }

        return back()->with('success', 'Data akun berhasil diperbarui!');
    }

    /**
     * Remove the specified user.
     */
    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, User $user): JsonResponse|RedirectResponse
    {
        if ($user->id === auth()->id()) {
            $errorMsg = 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.';
            if ($request->wantsJson() || $request->is('admin/api/*')) {
                return response()->json(['success' => false, 'message' => $errorMsg], 400);
            }
            return back()->with('error', $errorMsg);
        }

        // Cascade delete related records
        $user->devices()->delete();
        $user->apiKeys()->delete();
        $user->delete();

        if ($request->wantsJson() || $request->is('admin/api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Akun pengguna dan seluruh datanya berhasil dihapus!',
            ]);
        }

        return back()->with('success', 'Akun pengguna berhasil dihapus!');
    }

    /**
     * Suspend/Ban a user account with a custom announcement reason.
     */
    public function suspend(Request $request, User $user): JsonResponse|RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menangguhkan akun Anda sendiri.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $user->update([
            'status'         => 'suspended',
            'suspend_reason' => $validated['reason'],
        ]);

        if ($request->wantsJson() || $request->is('admin/api/*')) {
            return response()->json([
                'success' => true,
                'message' => "Akun {$user->name} berhasil disuspend.",
                'data'    => $user->fresh(),
            ]);
        }

        return back()->with('success', "Akun {$user->name} berhasil ditangguhkan/disuspend.");
    }

    /**
     * Reactivate/Unsuspend a user account.
     */
    public function activate(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $user->update([
            'status'         => 'active',
            'suspend_reason' => null,
        ]);

        if ($request->wantsJson() || $request->is('admin/api/*')) {
            return response()->json([
                'success' => true,
                'message' => "Akun {$user->name} telah diaktifkan kembali.",
                'data'    => $user->fresh(),
            ]);
        }

        return back()->with('success', "Akun {$user->name} telah diaktifkan kembali.");
    }
}
