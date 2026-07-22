<?php
// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\MidtransService;


class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan milik user yang sedang login.
     */
    public function index()
    {
        // PENTING: Jangan gunakan Order::all() !
        // Kita hanya mengambil order milik user yg sedang login menggunakan relasi hasMany.
        // auth()->user()->orders() akan otomatis memfilter: WHERE user_id = current_user_id
        $orders = auth()->user()->orders()
            ->with(['items.product']) // Eager Load nested: Order -> OrderItems -> Product
            ->latest() // Urutkan dari pesanan terbaru
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail satu pesanan.
     */
    public function show(Order $order, MidtransService $midtrans)
    {
        // Security
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Load relasi
        $order->load(['items.product', 'items.product.primaryImage']);
        
        // 🔥 TAMBAHKAN BARIS INI BRO: Paksa ambil data kolom paling ter-update dari tabel database
        $order->refresh();

        // 🔥 FIX: BUAT SNAP TOKEN HANYA JIKA BELUM ADA DAN BUKAN COD!
        if ($order->payment_status === 'unpaid' && strtolower($order->payment_method ?? '') !== 'cod' && !$order->snap_token) {
            $snapToken = $midtrans->createSnapToken($order);

            $order->update([
                'snap_token' => $snapToken
            ]);
            
            // Refresh lagi setelah update token biar datanya sinkron
            $order->refresh();
        }

        return view('orders.show', compact('order'));
    }

    public function convertToCod(Order $order)
    {
        // Pastikan pesanan memang belum dibayar sebelum diganti
        if ($order->payment_status === 'unpaid') {
            $order->update([
                'payment_method' => 'cod',
                'snap_token' => null // hapus snap token lama
            ]);
            
            // 🔥 TAMBAHKAN INI BRO: Paksa Laravel ambil data paling baru dari database
            $order->refresh(); 
            
            return redirect()->back()->with('success', 'Pembayaran akan dilakukan saat pesanan diterima (Cash on Delivery/COD).');
        }

        return redirect()->back()->with('error', 'Pesanan tidak dapat diubah.');
    }
}