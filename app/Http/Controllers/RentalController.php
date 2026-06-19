<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'duration'   => 'required|integer|min:1',
            'unit'       => 'required|in:day',
        ]);

        DB::transaction(function () use ($request) {

            $product = Product::findOrFail($request->product_id);

            $start = Carbon::now();
            $duration = (int) $request->duration;

            $end = $start->copy()->addDays($duration);

            // 🔥 HARGA
            $pricePerUnit = $product->rental_price ?? $product->price;

            // 🔥 FIX TOTAL (WAJIB)
            $totalPrice = $pricePerUnit * $request->quantity * $duration;

            // 🔥 CREATE RENTAL
            $rental = Rental::create([
                'user_id'     => auth()->id(),
                'start_date'  => $start,
                'end_date'    => $end,
                'status'      => 'ongoing',
                'total_price' => $totalPrice,
            ]);

            // 🔥 CREATE ITEM (FIX SEMUA)
            RentalItem::create([
                'rental_id'      => $rental->id,
                'product_id'     => $product->id,
                'quantity'       => $request->quantity,   // ✅ benar
                'price_per_unit' => $pricePerUnit,
                'unit'           => 'day',       // ✅ WAJIB
            ]);

            // 🔥 KURANGI STOK
            $product->decrement('stock', $request->quantity);
        });

        return back()->with('success', 'Produk berhasil disewa!');
    }
}