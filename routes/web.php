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
use App\Http\Controllers\RentalController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ChatController;


use App\Http\Controllers\Admin\LaporanPengaduanController;
use App\Http\Controllers\Admin\PendapatanController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LaporanController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }
    return view('welcome');
})->name('welcome');

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::view('/tentang', 'tentang')->name('tentang');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/sapa/{nama}', fn ($nama) => "Halo, $nama!");
Route::get('/kategori/{nama?}', fn ($nama = 'Semua') => "Kategori: $nama");

/*
|--------------------------------------------------------------------------
| CATALOG & RATING
|--------------------------------------------------------------------------
*/

Route::get('/products', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/products/{slug}', [CatalogController::class, 'show'])->name('catalog.show');

Route::get('/rating', [RatingController::class, 'index'])->name('rating.index');
Route::post('/rating', [RatingController::class, 'store'])->middleware('auth')->name('rating.store');

Route::post('/rentals', [RentalController::class, 'store'])->middleware('auth')->name('rentals.store');

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
| USER MIDDLEWARE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // PROFILE
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.updateAvatar');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::delete('/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
        Route::delete('/profile/google', [ProfileController::class, 'unlinkGoogle'])->name('profile.google.unlink');
    });

    // PASSWORD RESET
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendCode']);
    Route::get('/enter-code', [PasswordResetController::class, 'showEnterCodeForm'])->name('password.enter-code');
    Route::post('/enter-code', [PasswordResetController::class, 'verifyCode']);
    Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset-form');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    // EMAIL VERIFICATION
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/');
    })->middleware('signed')->name('verification.verify');

    // CART
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::post('/add', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/{item}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/{item}', [CartController::class, 'remove'])->name('cart.remove');
    });

    // WISHLIST
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // CHECKOUT & ORDERS
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/{order}/pay', [PaymentController::class, 'show'])->name('orders.pay');
        Route::get('/{order}/success', [PaymentController::class, 'success'])->name('orders.success');
        Route::get('/{order}/pending', [PaymentController::class, 'pending'])->name('orders.pending');
    });

    // BANTUAN/PENGADUAN USER
    Route::get('/bantuan', [LaporanPengaduanController::class, 'index'])->name('bantuan');
    Route::post('/bantuan', [LaporanPengaduanController::class, 'store'])->name('pengaduan.store');

    // Halaman utama chat (daftar kontak)
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    
    // Membuka ruang chat dengan user tertentu
    Route::get('/chat/{receiverId}', [ChatController::class, 'index'])->name('chat.show');
    
    // Mengirim pesan ke user tertentu
    Route::post('/chat/send/{receiverId}', [ChatController::class, 'sendMessage'])->name('chat.send');
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

    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/ratings', [RatingController::class, 'admin'])->name('ratings.index');

    // PRODUCTS
    Route::delete('products/bulk-delete', [AdminProductController::class, 'bulkDelete'])->name('products.bulkDelete');
    Route::delete('products/delete-all', [AdminProductController::class, 'deleteAll'])->name('products.deleteAll');
    Route::resource('products', AdminProductController::class);

    // CATEGORIES
    Route::delete('categories/bulk-delete', [AdminCategoryController::class, 'bulkDelete'])->name('categories.bulkDelete');
    Route::delete('categories/delete-all', [AdminCategoryController::class, 'deleteAll'])->name('categories.deleteAll');
    Route::resource('categories', AdminCategoryController::class);

    // ORDERS
    Route::delete('orders/bulk-delete', [AdminOrderController::class, 'bulkDelete'])->name('orders.bulk-delete');
    Route::delete('orders/delete-all', [AdminOrderController::class, 'deleteAll'])->name('orders.delete-all');
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('orders/pending', [AdminOrderController::class, 'pendingOrders'])->name('orders.pending');

    // USERS
    Route::resource('users', UserController::class)->only(['index', 'show', 'destroy']);

    // DASHBOARD EXTRA
    Route::get('stok-menipis', [AdminController::class, 'stokMenipis'])->name('stokmenipis');
    Route::get('total-produk', [AdminController::class, 'totalProduk'])->name('totalproduk');
    Route::get('proses', [AdminController::class, 'proses'])->name('proses');

    // RENTALS
    Route::post('/rentals/store', [RentalController::class, 'store'])->name('rentals.store');

    /*
    |--------------------------------------------------------------------------
    | STRATEGI PEMISAHAN 3 CONTROLLER (REPORTS, LAPORAN, PENGADUAN)
    |--------------------------------------------------------------------------
    */
    
    // 1. REPORT CONTROLLER (Laporan Penjualan / Sales & Pendapatan)
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/sales/export', [ReportController::class, 'exportSales'])->name('reports.export-sales');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index'); 

    // 2. LAPORAN CONTROLLER (Jika ada Laporan Finansial/Pendapatan Lain)
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/pendapatan', [PendapatanController::class, 'index'])->name('pendapatan');

    // 3. LAPORAN PENGADUAN CONTROLLER (Sesuai Tombol Detail di File Blade Anda)
    Route::get('/laporan-pengaduan', [LaporanPengaduanController::class, 'adminIndex'])
        ->name('laporanpengaduan.index');

    Route::get('/laporan-pengaduan/{id}', [LaporanPengaduanController::class, 'show'])
        ->name('laporanpengaduan.show'); 
        // ^ Menggunakan 'laporanpengaduan.show' agar sinkron dengan href di blade pengaduan Anda
});

/*
|--------------------------------------------------------------------------
| MIDTRANS
|--------------------------------------------------------------------------
*/

Route::post('midtrans/notification', [MidtransNotificationController::class, 'handle'])->name('midtrans.notification');