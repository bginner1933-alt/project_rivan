<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Menyuntikkan variabel data ke navbar secara otomatis dan aman
        View::composer('*', function ($view) {
            if (Auth::check()) {
                // Panggil CartService secara dinamis dari Service Container
                $cartService = app(CartService::class);
                $cart = $cartService->getCart();
                
                // Hitung total quantity dari item yang ada di keranjang
                $cartCount = $cart && $cart->items ? $cart->items->sum('quantity') : 0;
            } else {
                $cartCount = 0;
            }

            // Variabel $cartCount ini sekarang bisa dipakai di file Blade mana pun
            $view->with('cartCount', $cartCount);
        });
    }
}