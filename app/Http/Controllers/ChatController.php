<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    // 1. Tampilan Utama Chat
    public function index($receiverId = null)
    {
        $authId = Auth::id();
        $users = User::where('id', '!=', $authId)->get();
        $chats = collect();
        $receiver = null;

        if ($receiverId) {
            $receiver = User::findOrFail($receiverId);
            $chats = Chat::where(function ($query) use ($authId, $receiverId) {
                $query->where('sender_id', $authId)
                      ->where('receiver_id', $receiverId);
            })
            ->orWhere(function ($query) use ($authId, $receiverId) {
                $query->where('sender_id', $receiverId)
                      ->where('receiver_id', $authId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

            Chat::where('sender_id', $receiverId)
                ->where('receiver_id', $authId)
                ->update(['is_read' => true]);
        }

        return view('chat.index', compact('users', 'chats', 'receiver'));
    }

    // 2. Fungsi Kirim Pesan
    public function sendMessage(Request $request, $receiverId)
    {
        try {
            $request->validate([
                'message' => 'nullable|string',
                'image'   => 'nullable|image|max:2048',
            ]);

            if (empty(trim($request->message ?? '')) && !$request->hasFile('image')) {
                return response()->json(['success' => false, 'message' => 'Pesan tidak boleh kosong'], 422);
            }

            $path = null;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('chat-images', 'public');
            }

            $chat = Chat::create([
                'sender_id'   => Auth::id(),
                'receiver_id' => $receiverId,
                'message'     => $request->message,
                'image'       => $path,
            ]);

            broadcast(new MessageSent($chat))->toOthers();

            return response()->json(['success' => true, 'chat' => $chat]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 3. Hapus Pesan Terpilih (Untuk Diri Sendiri)
    public function deleteSelected(Request $request)
    {
        try {
            $ids = $request->input('ids');
            if (!$ids || !is_array($ids)) {
                return response()->json(['success' => false, 'message' => 'Pilih pesan dahulu']);
            }

            Chat::whereIn('id', $ids)
                ->where(function ($q) {
                    $q->where('sender_id', Auth::id())
                      ->orWhere('receiver_id', Auth::id());
                })
                ->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()], 500);
        }
    }

    // 4. Hapus Pesan Terpilih (Untuk Semua Orang)
    public function deleteSelectedAll(Request $request) 
    {
        try {
            $ids = $request->input('ids');
            
            if (!$ids || !is_array($ids)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Server tidak menerima daftar ID.'
                ], 400);
            }

            // Menghapus file gambar jika ada di storage sebelum data dihapus
            $chatsWithImages = Chat::whereIn('id', $ids)->whereNotNull('image')->get();
            foreach ($chatsWithImages as $chat) {
                if ($chat->image) {
                    Storage::disk('public')->delete($chat->image);
                }
            }

            // Proses Hapus Permanen
            $deletedCount = Chat::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => $deletedCount . ' pesan berhasil dihapus untuk semua orang.'
            ]);

        } catch (\Exception $e) {
            // Jika tabel belum ada atau kolom salah, pesan error aslinya akan muncul di sini
            return response()->json([
                'success' => false, 
                'message' => 'Laravel Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // 5. Hapus Satu Pesan (Fungsi Lama)
    public function destroy($id)
    {
        try {
            $chat = Chat::find($id);
            if ($chat) {
                $chat->delete();
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false, 'message' => 'Pesan tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}