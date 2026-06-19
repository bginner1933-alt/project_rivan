<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Membuat Order baru dari Keranjang Belanja User
     */
    public function createOrder(User $user, array $shippingData): Order
    {
        $cart = $user->cart;

        if (!$cart || $cart->items->isEmpty()) {
            throw new \Exception('Keranjang belanja kosong.');
        }

        return DB::transaction(function () use ($user, $cart, $shippingData) {

            $subtotal = 0;
            $shippingCost = 30000;

            // ✅ HITUNG TOTAL (SUDAH SUPPORT RENT)
            foreach ($cart->items as $item) {

                $product = $item->product()->lockForUpdate()->first();

                if ($item->quantity > $product->stock) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
                }

                $price = $item->price ?? $product->display_price;

                if ($item->type === 'rent') {
                    $subtotal += $price * $item->quantity * ($item->duration ?? 1);
                } else {
                    $subtotal += $price * $item->quantity;
                }
            }

            $totalAmount = $subtotal + $shippingCost;

            // ✅ BUAT ORDER
            $order = Order::create([
                'user_id'          => $user->id,
                'order_number'     => 'ORD-' . strtoupper(Str::random(10)),
                'status'           => 'pending',
                'payment_status'   => 'unpaid',
                'shipping_name'    => $shippingData['name'],
                'shipping_address' => $shippingData['address'],
                'shipping_phone'   => $shippingData['phone'],
                'total_amount'     => $totalAmount,
                'shipping_cost'    => $shippingCost,
            ]);

            // ✅ PINDAHKAN CART → ORDER ITEMS (INI YANG PENTING)
            foreach ($cart->items as $item) {
                $product = $item->product;

                $price = $item->price ?? $product->display_price;

                if ($item->type === 'rent') {
                    $subtotalItem = $price * $item->quantity * ($item->duration ?? 1);
                } else {
                    $subtotalItem = $price * $item->quantity;
                }

                $order->items()->create([
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'price'        => $price, // 🔥 FIX DI SINI
                    'quantity'     => $item->quantity,
                    'subtotal'     => $subtotalItem, // 🔥 FIX DI SINI
                ]);

                // kurangi stok
                $product->decrement('stock', $item->quantity);
            }

            // kosongkan cart
            $cart->items()->delete();

            return $order;
        });
    }
}