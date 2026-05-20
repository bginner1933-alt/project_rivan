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
    // =========================================================
    // 1. Tampilan Utama Chat
    // =========================================================
    public function index($receiverId = null)
    {
        $authId  = Auth::id();
        $users   = User::where('id', '!=', $authId)->get();
        $chats   = collect();
        $receiver = null;

        if ($receiverId) {
            $receiver = User::findOrFail($receiverId);

            $chats = Chat::where(function ($query) use ($authId, $receiverId) {
                        $query->where('sender_id', $authId)
                              ->where('receiver_id', $receiverId)
                              // Jangan tampilkan pesan yang sudah dihapus oleh pengirim (kita)
                              ->where('deleted_by_sender', false);
                    })
                    ->orWhere(function ($query) use ($authId, $receiverId) {
                        $query->where('sender_id', $receiverId)
                              ->where('receiver_id', $authId)
                              // Jangan tampilkan pesan yang sudah dihapus oleh penerima (kita)
                              ->where('deleted_by_receiver', false);
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();

            // Tandai pesan masuk sebagai sudah dibaca
            Chat::where('sender_id', $receiverId)
                ->where('receiver_id', $authId)
                ->update(['is_read' => true]);
        }

        return view('chat.index', compact('users', 'chats', 'receiver'));
    }

    // =========================================================
    // 2. Kirim Pesan
    // =========================================================
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

    // =========================================================
    // 3. Hapus Satu Pesan (Dropdown → Hapus)
    //    Menerima: scope = 'me' | 'all'
    // =========================================================
    public function destroy(Request $request, $id)
    {
        try {
            $authId = Auth::id();
            $scope  = $request->input('scope', 'me');

            $chat = Chat::find($id);

            if (!$chat) {
                return response()->json(['success' => false, 'message' => 'Pesan tidak ditemukan'], 404);
            }

            if ($scope === 'all') {
                // ── Hapus untuk semua ────────────────────────────────
                // Hanya boleh dilakukan oleh pengirim pesan
                if ($chat->sender_id !== $authId) {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin'], 403);
                }

                // Hapus file gambar jika ada
                if ($chat->image) {
                    Storage::disk('public')->delete($chat->image);
                }

                $chat->delete(); // hard delete, hilang dari kedua sisi

            } else {
                // ── Hapus untuk saya ────────────────────────────────
                // Tandai sesuai posisi user (pengirim atau penerima)
                if ($chat->sender_id === $authId) {
                    $chat->update(['deleted_by_sender' => true]);
                } elseif ($chat->receiver_id === $authId) {
                    $chat->update(['deleted_by_receiver' => true]);
                } else {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin'], 403);
                }

                // Jika kedua sisi sudah menghapus → hard delete untuk bersihkan DB
                $chat->refresh();
                if ($chat->deleted_by_sender && $chat->deleted_by_receiver) {
                    if ($chat->image) {
                        Storage::disk('public')->delete($chat->image);
                    }
                    $chat->delete();
                }
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =========================================================
    // 4. Hapus Pesan Terpilih (Bulk / Selection Mode)
    //    Menerima: ids = array, scope = 'me' | 'all'
    // =========================================================
    public function deleteSelected(Request $request)
    {
        try {
            $authId = Auth::id();
            $ids    = $request->input('ids');
            $scope  = $request->input('scope', 'me');

            if (!$ids || !is_array($ids)) {
                return response()->json(['success' => false, 'message' => 'Pilih pesan dahulu'], 422);
            }

            if ($scope === 'all') {
                // ── Hapus untuk semua ────────────────────────────────
                // Hanya pesan yang dikirim oleh kita yang boleh dihapus untuk semua
                $chats = Chat::whereIn('id', $ids)
                             ->where('sender_id', $authId)
                             ->get();

                foreach ($chats as $chat) {
                    if ($chat->image) {
                        Storage::disk('public')->delete($chat->image);
                    }
                    $chat->delete();
                }

            } else {
                // ── Hapus untuk saya ────────────────────────────────
                // Pesan yang kita kirim → tandai deleted_by_sender
                Chat::whereIn('id', $ids)
                    ->where('sender_id', $authId)
                    ->update(['deleted_by_sender' => true]);

                // Pesan yang kita terima → tandai deleted_by_receiver
                Chat::whereIn('id', $ids)
                    ->where('receiver_id', $authId)
                    ->update(['deleted_by_receiver' => true]);

                // Bersihkan pesan yang sudah dihapus oleh kedua sisi
                $toClean = Chat::whereIn('id', $ids)
                               ->where('deleted_by_sender', true)
                               ->where('deleted_by_receiver', true)
                               ->get();

                foreach ($toClean as $chat) {
                    if ($chat->image) {
                        Storage::disk('public')->delete($chat->image);
                    }
                    $chat->delete();
                }
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}