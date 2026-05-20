<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('chats', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
        $table->text('message')->nullable();
        $table->string('image')->nullable();
        $table->boolean('is_read')->default(false);
        
        // Kolom tambahan sesuai $fillable kamu
        $table->boolean('deleted_by_sender')->default(false);
        $table->boolean('deleted_by_receiver')->default(false);
        $table->boolean('deleted_for_everyone')->default(false);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
