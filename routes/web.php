<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MidtransNotificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\LaporanPengaduanController;

use App\Http\Controllers\Admin\PendapatanController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RentalController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Halaman utama - Redirect ke home jika sudah login, tampilkan welcome jika belum
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }
    return view('welcome');
})->name('welcome');

// Rute untuk halaman Home (Dashboard) saat tombol Home ditekan
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::view('/tentang', 'tentang');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/sapa/{nama}', fn ($nama) => "Halo, $nama!");
Route::get('/kategori/{nama?}', fn ($nama = 'Semua') => "Kategori: $nama");

// Catalog
Route::get('/products', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/products/{slug}', [CatalogController::class, 'show'])->name('catalog.show');

Route::post('/rentals', [RentalController::class, 'store'])
    ->middleware('auth')
    ->name('rentals.store');

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Auth::routes();

/*
|--------------------------------------------------------------------------
| GOOGLE LOGIN
|--------------------------------------------------------------------------
*/
Route::controller(GoogleController::class)->group(function () {
    Route::get('/auth/google', 'redirect')->name('auth.google');
    Route::get('/auth/google/callback', 'callback')->name('auth.google.callback');
});

/*
|--------------------------------------------------------------------------
| USER (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.updateAvatar');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::delete('/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');

        Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
        Route::post('/forgot-password', [PasswordResetController::class, 'sendCode']);
        Route::get('/enter-code', [PasswordResetController::class, 'showEnterCodeForm'])->name('password.enter-code');
        Route::post('/enter-code', [PasswordResetController::class, 'verifyCode']);
        Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset-form');
        Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
    });

    // Email verification
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->name('verification.send');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/');
    })->middleware('signed')->name('verification.verify');

    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::post('/add', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/{item}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/{item}', [CartController::class, 'remove'])->name('cart.remove');
    });

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Orders (User)
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');

        Route::get('/{order}/pay', [PaymentController::class, 'show'])->name('orders.pay');
        Route::get('/{order}/success', [PaymentController::class, 'success'])->name('orders.success');
        Route::get('/{order}/pending', [PaymentController::class, 'pending'])->name('orders.pending');
    });

    // Unlink Google
    Route::delete('/profile/google', [ProfileController::class, 'unlinkGoogle'])->name('profile.google.unlink');

    // Rute Bantuan & Informasi
    Route::get('/bantuan', [LaporanPengaduanController::class, 'index'])->name('bantuan');
    Route::post('/bantuan', [LaporanPengaduanController::class, 'store'])->name('pengaduan.store');
    
    Route::view('/tentang', 'tentang')->name('tentang');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Products
    Route::delete('products/bulk-delete', [AdminProductController::class, 'bulkDelete'])->name('products.bulkDelete');
    Route::delete('products/delete-all', [AdminProductController::class, 'deleteAll'])->name('products.deleteAll');
    Route::resource('products', AdminProductController::class);

    // Categories
    Route::delete('categories/bulk-delete', [AdminCategoryController::class, 'bulkDelete'])->name('categories.bulkDelete');
    Route::delete('categories/delete-all', [AdminCategoryController::class, 'deleteAll'])->name('categories.deleteAll');
    Route::resource('categories', AdminCategoryController::class);

    // Orders
    Route::delete('orders/bulk-delete', [AdminOrderController::class, 'bulkDelete'])->name('orders.bulk-delete');
    Route::delete('orders/delete-all', [AdminOrderController::class, 'deleteAll'])->name('orders.delete-all');
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('orders/pending', [AdminOrderController::class, 'pendingOrders'])->name('orders.pending');

    // Pendapatan
    Route::get('/pendapatan', [PendapatanController::class, 'index'])->name('pendapatan');

    // Reports
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/sales/export', [ReportController::class, 'exportSales'])->name('reports.export-sales');

    // Users
    Route::resource('users', UserController::class)->only(['index', 'show', 'destroy']);

    // Dashboard tambahan
    Route::get('stok-menipis', [AdminController::class, 'stokMenipis'])->name('stokmenipis');
    Route::get('total-produk', [AdminController::class, 'totalProduk'])->name('totalproduk');
    Route::get('proses', [AdminController::class, 'proses'])->name('proses');
    Route::get('pendapatan', [AdminController::class, 'pendapatan'])->name('pendapatan');

    // Rentals
    Route::post('/rentals/store', [RentalController::class, 'store'])->name('rentals.store');

    // Unlink Google
    Route::delete('/profile/google', [ProfileController::class, 'unlinkGoogle'])->name('profile.google.unlink');

    // Laporan Pengaduan
    Route::get('/laporanpengaduan', [AdminController::class, 'laporanPengaduan'])->name('laporanpengaduan.index');
    Route::get('/laporanpengaduan/{id}', [AdminController::class, 'show'])->name('laporanpengaduan.show');
});

/*
|--------------------------------------------------------------------------
| MIDTRANS
|--------------------------------------------------------------------------
*/
Route::post('midtrans/notification', [MidtransNotificationController::class, 'handle'])
    ->name('midtrans.notification');