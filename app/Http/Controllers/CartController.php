<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Models\Product;
use Illuminate\Http\Request;
use Exception;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * 🛒 Halaman Keranjang
     */
    public function index()
    {
        $cart = $this->cartService->getCart();

        // Eager load
        $cart->load(['items.product.primaryImage']);

        $cartItems = $cart->items->map(function ($item) {

            // 🔥 Tentukan harga fallback (jaga2 kalau DB kosong)
            if ($item->type === 'rent') {
                $price = $item->price ?? $item->product->rental_price;
                $subtotal = $price * $item->quantity * ($item->duration ?? 1);
            } else {
                $price = $item->price ?? $item->product->display_price;
                $subtotal = $price * $item->quantity;
            }

            return [
                'id'       => $item->id,
                'product'  => $item->product,
                'quantity' => $item->quantity,
                'type'     => $item->type ?? 'buy',
                'price'    => $price,
                'duration' => $item->duration,
                'unit'     => $item->unit,
                'subtotal' => $subtotal,
            ];
        });

        $totalQuantity = $cartItems->sum('quantity');
        $total = $cartItems->sum('subtotal');

        return view('cart.index', compact('cartItems', 'totalQuantity', 'total'));
    }

    /**
     * ➕ Tambah ke Cart (BUY & RENT)
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'type'       => 'required|in:buy,rent',
            'duration'   => 'nullable|integer|min:1',
            'unit'       => 'nullable|string'
        ]);

        try {
            $product = Product::findOrFail($request->product_id);

            $this->cartService->addProduct(
                $product,
                $request->quantity,
                $request->type,
                $request->duration ?? 1,
                $request->unit ?? 'day'
            );

            return back()->with('success', 'Berhasil masuk keranjang!');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * 🔄 Update Quantity
     */
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        try {
            $this->cartService->updateQuantity($itemId, $request->quantity);
            return back()->with('success', 'Keranjang diperbarui.');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * ❌ Hapus Item
     */
    public function remove($itemId)
    {
        try {
            $this->cartService->removeItem($itemId);

            return back()->with('success', 'Item berhasil dihapus.');

        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}