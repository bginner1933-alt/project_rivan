<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('p'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Admin user ready');

        /*
        |--------------------------------------------------------------------------
        | CATEGORY
        |--------------------------------------------------------------------------
        */
        $categories = ['Elektronik', 'Kamera', 'Aksesoris'];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat)],
                ['name' => $cat]
            );
        }

        $this->command->info('✅ Categories created');

        /*
        |--------------------------------------------------------------------------
        | PRODUCTS
        |--------------------------------------------------------------------------
        */
        $kameraCategory = Category::where('slug', 'kamera')->first();
        $aksesorisCategory = Category::where('slug', 'aksesoris')->first();

        // Produk 1
        $product1 = Product::updateOrCreate(
            ['slug' => 'kamera-sony-a7'],
            [
                'name' => 'Kamera Sony A7',
                'description' => 'Kamera profesional full frame',
                'price' => 15000000,
                'rental_price' => 150000,
                'rental_unit' => 'day',
                'stock' => 10,
                'category_id' => $kameraCategory->id ?? 1,
            ]
        );

        // Produk 2
        $product2 = Product::updateOrCreate(
            ['slug' => 'tripod'],
            [
                'name' => 'Tripod Kamera',
                'description' => 'Tripod ringan untuk fotografi',
                'price' => 300000,
                'rental_price' => 20000,
                'rental_unit' => 'day',
                'stock' => 15,
                'category_id' => $aksesorisCategory->id ?? 1,
            ]
        );

        // Produk 3
        $product3 = Product::updateOrCreate(
            ['slug' => 'memory-card'],
            [
                'name' => 'Memory Card 128GB',
                'description' => 'Kecepatan tinggi',
                'price' => 250000,
                'rental_price' => null,
                'rental_unit' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
            ]
        );

        $this->command->info('✅ Products created');

        /*
        |--------------------------------------------------------------------------
        | PRODUCT IMAGES
        |--------------------------------------------------------------------------
        */

        // hapus image lama biar gak dobel
        ProductImage::truncate();

        $product1->images()->createMany([
            [
                'image_path' => 'images/kotak.jpeg',
                'is_primary' => true,
            ],
        ]);

        $product2->images()->createMany([
            [
                'image_path' => 'images/kotak2.jpeg',
                'is_primary' => true,
            ],
        ]);

        $product3->images()->createMany([
            [
                'image_path' => 'images/kotak3.jpeg',
                'is_primary' => true,
            ],
        ]);

        $this->command->info('✅ Product images created');

        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed!');
        $this->command->info('📧 Login: admin@example.com / p');
    }
}