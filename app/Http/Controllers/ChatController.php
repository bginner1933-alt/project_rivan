<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // 1. Menampilkan daftar pengguna untuk diajak chat & ruang chat aktif
    public function index($receiverId = null)
    {
        $authId = Auth::id();
        
        // Ambil semua user lain untuk daftar kontak
        $users = User::where('id', '!=', $authId)->get();

        $chats = collect();
        $receiver = null;

        // Jika kita sedang membuka chat dengan user spesifik
        if ($receiverId) {
            $receiver = User::findOrFail($receiverId);

            // Ambil semua riwayat chat antara saya dan si penerima
            $chats = Chat::where(function($query) use ($authId, $receiverId) {
                $query->where('sender_id', $authId)->where('receiver_id', $receiverId);
            })->orWhere(function($query) use ($authId, $receiverId) {
                $query->where('sender_id', $receiverId)->where('receiver_id', $authId);
            })->orderBy('created_at', 'asc')->get();

            // Tandai pesan dari lawan bicara sebagai "sudah dibaca"
            Chat::where('sender_id', $receiverId)->where('receiver_id', $authId)->update(['is_read' => true]);
        }

        return view('chat.index', compact('users', 'chats', 'receiver'));
    }

    // 2. Menyimpan pesan baru yang dikirim
    public function sendMessage(Request $request, $receiverId)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ]);

        return redirect()->route('chat.show', $receiverId)->with('success', 'Pesan terkirim!');
    }
}