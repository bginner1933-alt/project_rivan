<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->dateTime('start_date');
    $table->dateTime('end_date');

    $table->enum('status', ['ongoing', 'returned', 'late'])->default('ongoing');

    $table->decimal('total_price', 15, 2)->default(0);

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};