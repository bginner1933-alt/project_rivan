<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            // true = pesan sudah dihapus oleh pengirim (hapus untuk saya)
            $table->boolean('deleted_by_sender')->default(false)->after('is_read');
            // true = pesan sudah dihapus oleh penerima (hapus untuk saya dari sisi penerima)
            $table->boolean('deleted_by_receiver')->default(false)->after('deleted_by_sender');
        });
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn(['deleted_by_sender', 'deleted_by_receiver']);
        });
    }
};