<?php
// ========================================
// FILE: routes/web.php
// FUNGSI: Mendefinisikan URL routes aplikasi
// ========================================

use Illuminate\Support\Facades\Route;

// Route default (sudah ada)
Route::get('/', function () {
    return view('welcome');
});

// ================================================
// TUGAS: Tambahkan route baru di bawah ini
// ================================================

Route::get('/tentang', function () {
    // ================================================
    // Route::get() = Tangani HTTP GET request
    // '/tentang'   = URL yang akan dihandle
    // function     = Kode yang dijalankan saat URL diakses
    // ================================================

    return view('tentang');
    // ↑ return view('tentang') = Tampilkan file tentang.blade.php
    // ↑ Laravel akan mencari di: resources/views/tentang.blade.php
});

Route::get('/sapa/{nama}', function ($nama) {return "Halo, $nama! Selamat datang di Toko Online.";
});

Route::get('/kategori/{nama?}', function ($nama = 'Semua') {return "Menampilkan kategori: $nama";
});

Route::get('/produk/{id}', function ($id = 'belum ada') {
    return "Detail produk #$id";
})->name('produk.detail');