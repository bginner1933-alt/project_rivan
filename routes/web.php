<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProductController;

/*
|--------------------------------------------------------------------------
| HALAMAN UMUM (TANPA LOGIN)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tentang', function () {
    return view('tentang');
});

Route::get('/sapa/{nama}', function ($nama) {
    return "Halo, $nama! Selamat datang di Toko Online.";
});

Route::get('/kategori/{nama?}', function ($nama = 'Semua') {
    return "Menampilkan kategori: $nama";
});

Route::get('/produk/{id}', function ($id) {
    return "Detail produk #$id";
})->name('produk.detail');


/*
|--------------------------------------------------------------------------
| AUTH ROUTES (LOGIN, REGISTER, LOGOUT, DLL)
|--------------------------------------------------------------------------
*/

Auth::routes();


/*
|--------------------------------------------------------------------------
| ROUTE SETELAH LOGIN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/home', [HomeController::class, 'index'])
        ->name('home');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::resource('/products', AdminProductController::class);
    });


/*
|--------------------------------------------------------------------------
| GOOGLE OAUTH (SOCIALITE)
|--------------------------------------------------------------------------
*/

Route::controller(GoogleController::class)->group(function () {

    // Redirect ke Google
    Route::get('/auth/google', 'redirect')
        ->name('auth.google');

    // Callback dari Google
    Route::get('/auth/google/callback', 'callback')
        ->name('auth.google.callback');
});
