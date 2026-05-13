<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HALAMAN USER
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $ratings = Rating::latest()->get();
        $average = Rating::avg('star');

        return view('rating.index', compact('ratings', 'average'));
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN RATING
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
{
    $validated = $request->validate([
        'order_id' => 'required|exists:orders,id',
        'rating'   => 'required|integer|min:1|max:5',
        'message'  => 'required|string|max:1000',
    ]);

    // --- TAMBAHKAN CEK DISINI ---
    $exists = Rating::where('order_id', $validated['order_id'])->exists();

    if ($exists) {
        return back()->with('success', 'terima kasih untuk ulasan Anda.');
    }
    // ----------------------------

    Rating::create([
        'name'     => auth()->user()->name,
        'star'     => $validated['rating'],
        'message'  => $validated['message'],
        'order_id' => $validated['order_id'],
    ]);

    return back()->with('success', 'Rating berhasil dikirim!');
}

    /*
    |--------------------------------------------------------------------------
    | HALAMAN ADMIN
    |--------------------------------------------------------------------------
    */
    public function admin()
    {
        // Mengambil data rating terbaru dengan pagination
        $ratings = Rating::latest()->paginate(10);

        return view('admin.ratings.index', compact('ratings'));
    }
}