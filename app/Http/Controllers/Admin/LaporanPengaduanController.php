<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\LaporanPengaduan;
use App\Http\Controllers\Controller;

class LaporanPengaduanController extends Controller
{
    public function index()
    {
        return view('bantuan    ');
    }

    public function store(Request $request)
    {
        // 1. Validasi input form
        $request->validate([
            'name'       => 'required|string|max:255',
            'order_id'   => 'nullable|string|max:50',
            'category'   => 'required|string',
            'message'    => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // 2. Proses upload file jika ada
        $path = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('laporan_pengaduan', $filename, 'public');
        }

        // 3. Simpan ke database
        LaporanPengaduan::create([
            'name'            => $request->name,
            'order_id'        => $request->order_id,
            'category'        => $request->category,
            'message'         => $request->message,
            'attachment_path' => $path,
        ]);

        // 4. Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Laporan pengaduan berhasil dikirim. Tim kami akan segera membantu Anda!');
    }
}