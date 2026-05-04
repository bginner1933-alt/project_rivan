<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;


class ProfileController extends Controller
{
    /**
     * Menampilkan form edit profil.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            // Kirim data user yang sedang login ke view
            'user' => $request->user(),
        ]);
    }

    /**
     * Mengupdate informasi profil user.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Update Data Text (Nama, Email, dll)
        // fill() mengisi atribut model dengan data validasi, tapi belum disimpan ke DB.
        // Ini lebih aman daripada $user->update() langsung karena kita mau cek 'isDirty' dulu.
        $user->fill($request->validated());

        // 2. Cek Perubahan Email
        // Jika email berubah, kita harus membatalkan status verifikasi email (isDirty cek perubahan di memory).
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 3. Simpan ke Database
        // Method save() baru benar-benar menjalankan query UPDATE ke database.
        $user->save();

        return Redirect::route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update avatar user.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
        ]);

        $user = $request->user();

        // Hapus avatar lama
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Upload avatar baru
        $filename = 'avatar-' . $user->id . '-' . time() . '.' . $request->file('avatar')->extension();
        $path = $request->file('avatar')->storeAs('avatars', $filename, 'public');

        $user->update(['avatar' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Menghapus avatar (tombol "Hapus Foto").
     */
    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Hapus file fisik
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);

            // Set kolom di database jadi NULL
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }


    /**
     * Update password user.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Menghapus akun user permanen.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validasi password untuk keamanan sebelum hapus akun
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Logout dulu
        Auth::logout();

        // Hapus avatar fisik user sebelum hapus data user
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Hapus data user dari DB
        $user->delete();

        // Invalidate session agar tidak bisa dipakai lagi (Security)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Unlink Google account.
     */
    public function unlinkGoogle(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'google_id' => null,
            'avatar' => null, // Reset avatar ke default jika menggunakan Google avatar
        ]);

        return back()->with('success', 'Akun Google berhasil diputuskan.');
    }
}