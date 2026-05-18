<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\LaporanPengaduan;
use App\Http\Controllers\Controller;

class LaporanPengaduanController extends Controller
{
    /**
     * Menampilkan HALAMAN ADMIN (Daftar Pengaduan Pelanggan)
     */
    public function adminIndex()
    {
        // Mengambil data pengaduan terbaru dan membaginya menjadi 10 data per halaman
        $laporan = LaporanPengaduan::latest()->paginate(10);

        // Mengarah ke file blade admin yang berisi tabel data modern Anda tadi
        // Pastikan nama file blade Anda sesuai (misal: resources/views/admin/reports/index.blade.php)
        return view('admin.reports.index', compact('laporan'));
    }

    /**
     * Menampilkan HALAMAN USER (Form Input Pengaduan / Bantuan)
     */
    public function index()
    {
        return view('bantuan');
    }

    /**
     * Menyimpan input pengaduan dari Form User ke Database
     */
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

    public function show($id)
    {
        $item = LaporanPengaduan::findOrFail($id);
        return view('admin.reports.show', compact('item')); // Sesuaikan dengan nama file blade detail Anda
    }
}
