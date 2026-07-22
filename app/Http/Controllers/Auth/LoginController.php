<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter; // WAJIB ADA INI

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override method bawaan untuk menangani gagal login
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $throttleKey = $this->throttleKey($request);
        
        // Menambah hitung percobaan gagal
        RateLimiter::hit($throttleKey, 60);
        $attempts = RateLimiter::attempts($throttleKey);

        // Jika sudah 3 kali gagal, berikan pesan khusus
        if ($attempts >= 3) {
            throw ValidationException::withMessages([
                $this->username() => [
                    ' lupa password, <a href="'.route('password.request').'">Reset Password</a> belum punya akun? silakan <a href="'.route('register').'">Daftar Gratis</a>.'
                ],
            ]);
        }

        // Pesan gagal standar untuk percobaan 1 dan 2
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }
}