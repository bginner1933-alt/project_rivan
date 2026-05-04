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

        $code = Str::random(6); // Kode 6 karakter
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($code), 'created_at' => now()]
        );

        // Kirim email (buat Mailable jika belum)
        Mail::to($request->email)->send(new PasswordResetCode($code));

        return redirect()->route('password.enter-code')->with('email', $request->email);
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
            return back()->withErrors(['code' => 'Kode salah atau expired.']);
        }

        return redirect()->route('password.reset-form')->with('email', session('email'));
    }

    // Halaman update password
    public function showResetForm()
    {
        return view('auth.reset-password');
    }

    // Update password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', session('email'))->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('email', session('email'))->delete();

        return redirect()->route('login')->with('success', 'Password berhasil diupdate.');
    }
}