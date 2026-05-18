<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * LIST SEMUA ORDER + FILTER STATUS
     */
    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->whereRaw('LOWER(TRIM(status)) = ?', [
                    strtolower(trim($request->status))
                ]);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * ORDER YANG PERLU DIPROSES
     */
    public function pendingOrders()
    {
        dd(Order::pluck('status'));
    }

    /**
     * DETAIL ORDER
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'user']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * UPDATE STATUS ORDER
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $oldStatus = strtolower($order->status);
        $newStatus = strtolower($request->status);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        // anggap field payment_status = paid / unpaid
        $paymentStatus = strtolower($order->payment_status ?? 'unpaid');

        // kalau belum bayar
        if ($paymentStatus !== 'paid') {

            // tidak boleh processing/completed
            if (in_array($newStatus, ['processing', 'completed'])) {

                return back()->with(
                    'error',
                    'Pesanan belum dibayar, status tidak bisa diubah.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RESTOCK JIKA CANCEL
        |--------------------------------------------------------------------------
        */

        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {

            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS
        |--------------------------------------------------------------------------
        */

        $order->update([
            'status' => $newStatus
        ]);

        return back()->with(
            'success',
            "Status diperbarui menjadi $newStatus"
        );
    }

    /**
     * BULK DELETE
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('selected_ids', []);

        if (!$ids) {
            return back()->with('error', 'Tidak ada pesanan dipilih.');
        }

        DB::beginTransaction();

        try {
            $orders = Order::with('items.product')
                ->whereIn('id', $ids)
                ->get();

            foreach ($orders as $order) {
                if ($order->status !== 'cancelled') {
                    foreach ($order->items as $item) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }

                $order->items()->delete();
            }

            Order::whereIn('id', $ids)->delete();

            DB::commit();

            return back()->with('success', 'Pesanan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * HAPUS SEMUA ORDER
     */
    public function deleteAll()
    {
        try {
            Order::query()->delete();

            return back()->with('success', 'Semua order berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}