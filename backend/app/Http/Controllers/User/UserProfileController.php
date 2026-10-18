<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    /**
     * Display the user's profile and password management screen.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $package = $user->getPackage();

        return view('user.profile', compact('user', 'package'));
    }

    /**
     * Update the user's basic profile details (Name & Email).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ], [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format alamat email tidak valid.',
            'email.unique'   => 'Alamat email ini sudah terdaftar pada akun lain.',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        return redirect()->route('user.profile')
            ->with('success', 'Informasi profil Anda berhasil diperbarui!');
    }

    /**
     * Update the user's password securely with current password verification.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required'         => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required'                 => 'Password baru wajib diisi.',
            'password.min'                      => 'Password baru minimal harus 8 karakter.',
            'password.confirmed'                => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('user.profile')
            ->with('success', 'Password Anda berhasil diperbarui secara aman.');
    }
}
