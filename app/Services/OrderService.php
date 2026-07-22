<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // 🔥 WAJIB TAMBAHKAN INI DI ATAS

class OrderService
{
    public function createOrder(User $user, array $shippingData): Order
    {
        $cart = $user->cart;

        if (!$cart || $cart->items->isEmpty()) {
            throw new \Exception('Keranjang belanja kosong.');
        }

        // 🔍 FIX 1: PENGAMAN DATA - Kita paksa ambil dari key apa saja yang dikirim controller
        // Kita test apakah array-nya membawa data berat
        $weightCost = 0;
        if (isset($shippingData['weight_cost'])) {
            $weightCost = (float) $shippingData['weight_cost'];
        } elseif (isset($shippingData['biaya_berat'])) {
            $weightCost = (float) $shippingData['biaya_berat'];
        }

        $shippingCost = isset($shippingData['shipping_cost']) ? (float) $shippingData['shipping_cost'] : 0;

        // 📝 DEBUG LOG: Cek folder storage/logs/laravel.log setelah kamu checkout!
        Log::info('Data Masuk ke OrderService:', [
            'raw_shipping_data' => $shippingData,
            'parsed_shipping_cost' => $shippingCost,
            'parsed_weight_cost' => $weightCost
        ]);

        return DB::transaction(function () use ($user, $cart, $shippingData, $shippingCost, $weightCost) {

            $subtotal = 0;

            // HITUNG TOTAL DARI CART ITEMS
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

            // TOTAL AMOUNT = SUB + ONGKIR + BIAYA BERAT
            $totalAmount = $subtotal + $shippingCost + $weightCost;

            // 🔍 FIX 2: BIKIN ORDER DENGAN NAMA PROPERTI YANG DIKUNCI PAS
            $order = Order::create([
                'user_id'          => $user->id,
                'order_number'     => 'ORD-' . strtoupper(Str::random(10)),
                'status'           => 'pending',
                'payment_status'   => 'unpaid',
                'shipping_name'    => $shippingData['name'],
                'shipping_address' => $shippingData['address'],
                'shipping_phone'   => $shippingData['phone'],
                
                // Masukkan nominal ke kolom database
                'weight_cost'      => $weightCost, 
                'shipping_cost'    => $shippingCost,
                'total_amount'     => $totalAmount,
                'discount_price'   => $totalAmount, 
                'payment_method'   => $shippingData['payment_method'] ?? 'midtrans',
            ]);

            // PINDAHKAN CART → ORDER ITEMS
            foreach ($cart->items as $item) {
                $product = $item->product;
                $price = $item->price ?? $product->display_price;

                $subtotalItem = ($item->type === 'rent')
                    ? $price * $item->quantity * ($item->duration ?? 1)
                    : $price * $item->quantity;

                $order->items()->create([
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'price'        => $price,
                    'quantity'     => $item->quantity,
                    'subtotal'     => $subtotalItem,
                ]);

                $product->decrement('stock', $item->quantity);
            }

            $cart->items()->delete();

            return $order;
        });
    }
}