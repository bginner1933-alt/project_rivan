<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chatId;
    public $receiverId;
    public $scope;

    // ✅ Tambah $scope di constructor
    public function __construct($chatId, $receiverId, $scope = 'all')
    {
        $this->chatId     = $chatId;
        $this->receiverId = $receiverId;
        $this->scope      = $scope;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('chat.' . $this->receiverId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageDeleted';
    }

    // ✅ Kirim scope ke frontend
    public function broadcastWith(): array
    {
        return [
            'chat_id'     => $this->chatId,
            'receiver_id' => $this->receiverId,
            'scope'       => $this->scope,
        ];
    }
}