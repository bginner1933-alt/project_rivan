<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
   Schema::table('products', function (Blueprint $table) {
    $table->decimal('rental_price', 15, 2)->nullable()->after('price');
    $table->string('rental_unit')->nullable()->after('rental_price');
});
}

public function down(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn(['rental_price', 'rental_unit']);
    });
}
};
