<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        // Ambil order berdasarkan order_number
        $order = Order::where('order_number', $request->order_id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $paymentType = $request->payment_type;
        $fraudStatus = $request->fraud_status ?? null;

        // ✅ LOGIKA UTAMA
        if ($transactionStatus == 'capture') {
            if ($paymentType == 'credit_card') {
                if ($fraudStatus == 'challenge') {
                    $order->payment_status = 'pending';
                } else {
                    $order->payment_status = 'paid';
                }
            }
        } 
        elseif ($transactionStatus == 'settlement') {
            $order->payment_status = 'paid';
        } 
        elseif ($transactionStatus == 'pending') {
            $order->payment_status = 'pending';
        } 
        elseif ($transactionStatus == 'deny') {
            $order->payment_status = 'failed';
        } 
        elseif ($transactionStatus == 'expire') {
            $order->payment_status = 'expired';
        } 
        elseif ($transactionStatus == 'cancel') {
            $order->payment_status = 'cancelled';
        }

        $order->save();

        return response()->json(['message' => 'Callback berhasil']);
    }
}