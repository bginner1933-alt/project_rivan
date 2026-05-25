<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Storage;
use App\Events\MessageDeleted;

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
    // 3. Hapus Satu Pesan
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

            // Simpan receiver sebelum dihapus (untuk broadcast)
            $receiverId = $chat->receiver_id;
            $senderId   = $chat->sender_id;

            if ($scope === 'all') {
                if ($chat->sender_id !== $authId) {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin'], 403);
                }

                if ($chat->image) {
                    Storage::disk('public')->delete($chat->image);
                }

                $chat->delete();

                // ✅ Broadcast ke penerima — hapus dari kedua sisi
                broadcast(new MessageDeleted($id, $receiverId, 'all'))->toOthers();

            } else {
                if ($chat->sender_id === $authId) {
                    $chat->update(['deleted_by_sender' => true]);
                } elseif ($chat->receiver_id === $authId) {
                    $chat->update(['deleted_by_receiver' => true]);
                } else {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin'], 403);
                }

                $chat->refresh();
                if ($chat->deleted_by_sender && $chat->deleted_by_receiver) {
                    if ($chat->image) {
                        Storage::disk('public')->delete($chat->image);
                    }
                    $chat->delete();
                }

                // ✅ Broadcast 'me' — hanya hapus di sisi pengirim (tidak perlu broadcast ke lawan)
                // Tidak perlu broadcast karena hanya mempengaruhi diri sendiri
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =========================================================
    // 4. Hapus Pesan Terpilih (Bulk)
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
                $chats = Chat::whereIn('id', $ids)
                            ->where('sender_id', $authId)
                            ->get();

                foreach ($chats as $chat) {
                    if ($chat->image) {
                        Storage::disk('public')->delete($chat->image);
                    }

                    $receiverId = $chat->receiver_id;
                    $chat->delete();

                    // ✅ Broadcast tiap pesan yang dihapus
                    broadcast(new MessageDeleted($chat->id, $receiverId, 'all'))->toOthers();
                }

            } else {
                Chat::whereIn('id', $ids)
                    ->where('sender_id', $authId)
                    ->update(['deleted_by_sender' => true]);

                Chat::whereIn('id', $ids)
                    ->where('receiver_id', $authId)
                    ->update(['deleted_by_receiver' => true]);

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