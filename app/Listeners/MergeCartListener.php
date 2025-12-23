<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\CartService;

class MergeCartListener
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // user yang baru login
        $user = $event->user;

        $cartService = new CartService();
        $cartService->mergeCartOnLogin($user);
    }
}
