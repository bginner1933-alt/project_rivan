<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Exception;

class CartService
{
    /**
     * Mendapatkan (atau membuat) keranjang untuk user saat ini.
     * Menggunakan Session ID untuk guest, dan User ID untuk member.
     */
    public function getCart(): Cart
    {
        if (Auth::check()) {
            // Skenario 1: User Login
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        } else {
            // Skenario 2: Guest (Belum Login)
            $sessionId = Session::getId();
            return Cart::firstOrCreate(['session_id' => $sessionId]);
        }
    }

    /**
     * Menambahkan produk ke keranjang.
     * Handle logika: Baru vs Existing, dan Cek Stok Dinamis.
     */
    public function addProduct(Product $product, int $quantity = 1, string $type = 'buy', int $duration = 1, string $unit = 'day'): void
    {
        $cart = $this->getCart();

        // 🔥 Tentukan harga
        if ($type === 'rent') {
            if (!$product->rental_price) {
                throw new Exception("Produk ini tidak bisa disewa");
            }
            $price = $product->rental_price;
        } else {
            $price = $product->display_price;
        }

        // 🔍 Cek existing item (lebih spesifik)
        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('type', $type)
            ->where('duration', $type === 'rent' ? $duration : null)
            ->where('unit', $type === 'rent' ? $unit : null)
            ->first();

        if ($existingItem) {

            $newQuantity = $existingItem->quantity + $quantity;

            if ($newQuantity > $product->stock) {
                throw new Exception("Stok tinggal " . $product->stock);
            }

            $existingItem->update(['quantity' => $newQuantity]);

        } else {

            if ($quantity > $product->stock) {
                throw new Exception("Stok tinggal " . $product->stock);
            }

            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'type'       => $type,
                'price'      => $price,
                'duration'   => $type === 'rent' ? $duration : null,
                'unit'       => $type === 'rent' ? $unit : null,
            ]);
        }

        $cart->touch();
    }

    /**
     * Mengupdate jumlah item (misal dari input jumlah di halaman keranjang).
     */
    public function updateQuantity(int $itemId, int $quantity): void
    {
        $item = CartItem::findOrFail($itemId);
        $product = $item->product;

        // Security Check: Pastikan item milik cart user yang aktif
        $this->verifyCartOwnership($item->cart);

        // Validasi Stok Real-time
        if ($quantity > $product->stock) {
            throw new Exception("Stok tinggal " . $product->stock);
        }

        if ($quantity <= 0) {
            $item->delete(); // Jika diupdate jadi 0 atau minus, hapus.
        } else {
            $item->update(['quantity' => $quantity]);
        }
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function removeItem(int $itemId): void
    {
        $item = CartItem::findOrFail($itemId);

        // Security Check
        $this->verifyCartOwnership($item->cart);

        $item->delete();
    }

    /**
     * Menggabungkan keranjang Guest ke User saat Login.
     */
    public function mergeCartOnLogin(): void
    {
        $sessionId = Session::getId();
        $guestCart = Cart::where('session_id', $sessionId)->with('items.product')->first();

        if (!$guestCart) return;

        $userCart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        foreach ($guestCart->items as $item) {
            $existingUserItem = $userCart->items()
                ->where('product_id', $item->product_id)
                ->first();

            if ($existingUserItem) {
                // Merge quantity dan pastikan tidak melebihi stok yang ada
                $combinedQuantity = $existingUserItem->quantity + $item->quantity;
                $finalQuantity = min($combinedQuantity, $item->product->stock);
                
                $existingUserItem->update(['quantity' => $finalQuantity]);
            } else {
                // Pastikan item yang dipindah tidak melebihi stok saat ini
                $finalQuantity = min($item->quantity, $item->product->stock);
                $item->update([
                    'cart_id' => $userCart->id,
                    'quantity' => $finalQuantity
                ]);
            }
        }

        // Hapus keranjang tamu setelah dipindahkan
        $guestCart->delete();
    }

    /**
     * Helper untuk keamanan (IDOR Protection)
     */
    private function verifyCartOwnership(Cart $cart): void
    {
        $currentCart = $this->getCart();
        if ($cart->id !== $currentCart->id) {
            abort(403, 'Akses ditolak. Ini bukan keranjang Anda.');
        }
    }
}