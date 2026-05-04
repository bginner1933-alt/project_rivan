<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Tampilkan halaman checkout
     */
    public function index()
    {
        $user = auth()->user();
        $cart = $user->cart;

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang kosong.');
        }

       $subtotal = 0;

        foreach ($cart->items as $item) {

            $price = $item->price ?? 0;

            if ($item->type === 'rent') {
                $subtotal += $price * $item->quantity * ($item->duration ?? 1);
            } else {
                $subtotal += $price * $item->quantity;
            }
        }

        $shippingCost = 20000;
        $total = $subtotal + $shippingCost;

        return view('checkout.index', compact(
            'cart',
            'subtotal',
            'shippingCost',
            'total'
        ));
    }

    /**
     * Simpan pesanan
     */
    public function store(Request $request, OrderService $orderService)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        try {
            $order = $orderService->createOrder(
                auth()->user(),
                $validated
            );

            return redirect()
                ->route('orders.show', $order->id)
                ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
