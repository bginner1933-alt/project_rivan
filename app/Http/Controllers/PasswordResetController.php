<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\PasswordResetCode;

class PasswordResetController extends Controller
{
    // Halaman forgot password
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Kirim kode ke email
    public function sendCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $code = Str::random(6); 
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($code), 'created_at' => now()]
        );

        Mail::to($request->email)->send(new PasswordResetCode($code));

        // Simpan email ke session agar bisa dipakai di langkah berikutnya
        session(['email' => $request->email]);

        return redirect()->route('password.enter-code')->with('status', 'Kode verifikasi sudah dikirim ke email kamu!');
    }

    // Halaman masukkan kode
    public function showEnterCodeForm()
    {
        return view('auth.enter-code');
    }

    // Verifikasi kode
    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $reset = DB::table('password_resets')->where('email', session('email'))->first();
        
        if (!$reset || !Hash::check($request->code, $reset->token) || now()->diffInMinutes($reset->created_at) > 10) {
            return back()->withErrors(['code' => 'Waduh, kode salah atau sudah kadaluarsa (expired) nih!']);
        }

        return redirect()->route('password.reset-form');
    }

    // Halaman update password
    public function showResetForm()
    {
        // Pastikan session email masih ada
        if (!session()->has('email')) {
            return redirect()->route('password.forgot')->withErrors(['email' => 'Sesi habis, silakan ulangi proses lupa password.']);
        }
        return view('auth.reset-password');
    }

    // Update password
    public function resetPassword(Request $request)
    {
        // Validasi dengan pesan custom yang lebih personal
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Password-nya diisi dulu ya, bro!',
            'password.min' => 'Waduh, sandinya minimal harus 8 karakter ya!',
            'password.confirmed' => 'Yah, konfirmasi password-nya belum sama nih.',
        ]);

        $user = User::where('email', session('email'))->first();
        
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            // Hapus data reset setelah berhasil
            DB::table('password_resets')->where('email', session('email'))->delete();
            session()->forget('email');

            return redirect()->route('login')->with('success', 'Password berhasil diupdate. Sekarang sudah aman, silakan login!');
        }

        return back()->withErrors(['email' => 'User tidak ditemukan.']);
    }
}