<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class FixProductDiscounts extends Command
{
    protected $signature = 'products:fix-discounts';
    protected $description = 'Fix invalid product discounts where discount_price >= price';

    public function handle()
    {
        // Cari produk dengan discount_price >= price
        $invalidProducts = Product::whereNotNull('discount_price')
            ->whereRaw('discount_price >= price')
            ->get();

        if ($invalidProducts->isEmpty()) {
            $this->info('✓ Semua produk sudah benar - tidak ada discount yang invalid.');
            return Command::SUCCESS;
        }

        $this->warn("⚠ Ditemukan {$invalidProducts->count()} produk dengan discount tidak valid:");

        foreach ($invalidProducts as $product) {
            $this->line("  - {$product->name}: Price={$product->price}, Discount={$product->discount_price}");
        }

        if ($this->confirm('Hapus discount dari produk-produk ini?')) {
            Product::whereNotNull('discount_price')
                ->whereRaw('discount_price >= price')
                ->update(['discount_price' => null]);

            $this->info("✓ {$invalidProducts->count()} produk sudah diperbaiki - discount dihapus.");
            return Command::SUCCESS;
        }

        $this->info('Batal.');
        return Command::FAILURE;
    }
}
