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
        $categories = ['Kerajinan', 'Aksesoris', 'Seserahan'];

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
        $kerajinanCategory = Category::where('slug', 'kerajinan')->first();
        $aksesorisCategory = Category::where('slug', 'aksesoris')->first();
        $seserahanCategory = Category::where('slug', 'seserahan')->first();

        // Produk 1
        $product1 = Product::updateOrCreate(
            ['slug' => 'kayu-box'],
            [
                'name' => 'Kotak Penyimpanan Kayu',
                'description' => 'Dari Kayu Premium',
                'price' => 50000,
                'rental_price' => null,
                'rental_duration' => null,
                'stock' => 10,
                'category_id' => $kerajinanCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        // Produk 2
        $product2 = Product::updateOrCreate(
            ['slug' => 'souvenir'],
            [
                'name' => 'Souvenir',
                'description' => 'Souvenir unik untuk acara pernikahan',
                'price' => null,
                'rental_price' => 50000,
                'rental_duration' => 3,
                'stock' => 15,
                'category_id' => $seserahanCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        // Produk 3
        $product3 = Product::updateOrCreate(
            ['slug' => 'souvenir1'],
            [
                'name' => 'Souvenir',
                'description' => 'Souvenir unik untuk acara pernikahan',
                'price' => null,
                'rental_price' => 50000,
                'rental_duration' => 5,
                'stock' => 50,
                'category_id' => $seserahanCategory->id ?? 1,
                'is_featured' => false,
            ]
        );

        // Produk 4
        $product5 = Product::updateOrCreate(
            ['slug' => 'souvenir2'],
            [
                'name' => 'Souvenir',
                'description' => 'Souvenir unik untuk acara pernikahan',
                'price' => null,
                'rental_price' => 50000,
                'rental_duration' => 7,
                'stock' => 30,
                'category_id' => $seserahanCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        // Produk 5
        $product6 = Product::updateOrCreate(
            ['slug' => 'souvenir3'],
            [
                'name' => 'Souvenir',
                'description' => 'Souvenir unik untuk acara pernikahan',
                'price' => null,
                'rental_price' => 50000,
                'rental_duration' => 10,
                'stock' => 30,
                'category_id' => $seserahanCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        // Produk 6
        $product7 = Product::updateOrCreate(
            ['slug' => 'souvenir4'],
            [
                'name' => 'Souvenir Premium',
                'description' => 'Souvenir premium untuk acara eksklusif',
                'price' => null,
                'rental_price' => 75000,
                'rental_duration' => 14,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        // Produk 7
        $product8 = Product::updateOrCreate(
            ['slug' => 'cincin1'],
            [
                'name' => 'Cincin Pernikahan',
                'description' => 'Cincin untuk acara pernikahan',
                'price' => 450000,
                'rental_price' => null,
                'rental_duration' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        // Produk 8
        $product9 = Product::updateOrCreate(
            ['slug' => 'cincin2'],
            [
                'name' => 'Cincin Emas',
                'description' => 'Cincin emas elegan',
                'price' => 550000,
                'rental_price' => null,
                'rental_duration' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        // Produk 9
        $product10 = Product::updateOrCreate(
            ['slug' => 'cincin3'],
            [
                'name' => 'Cincin Silver',
                'description' => 'Cincin silver mewah',
                'price' => 350000,
                'rental_price' => null,
                'rental_duration' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        // Produk 10
        $product11 = Product::updateOrCreate(
            ['slug' => 'cincin4'],
            [
                'name' => 'Cincin Diamond',
                'description' => 'Cincin berlian premium',
                'price' => 950000,
                'rental_price' => null,
                'rental_duration' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        // Produk 11
        $product12 = Product::updateOrCreate(
            ['slug' => 'cincin5'],
            [
                'name' => 'Cincin Elegant',
                'description' => 'Desain modern dan elegan',
                'price' => 650000,
                'rental_price' => null,
                'rental_duration' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        // Produk 12
        $product14 = Product::updateOrCreate(
            ['slug' => 'cincin6'],
            [
                'name' => 'Cincin Platinum',
                'description' => 'Cincin platinum eksklusif',
                'price' => 1250000,
                'rental_price' => null,
                'rental_duration' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        // Produk 13
        $product15 = Product::updateOrCreate(
            ['slug' => 'cincin7'],
            [
                'name' => 'Cincin Luxury',
                'description' => 'Luxury wedding ring',
                'price' => 1750000,
                'rental_price' => null,
                'rental_duration' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        // Produk 14
        $product16 = Product::updateOrCreate(
            ['slug' => 'wadah-kayu'],
            [
                'name' => 'Wadah Kayu',
                'description' => 'Wadah untuk menyimpan',
                'price' => 50000,
                'rental_price' => null,
                'rental_duration' => null,
                'stock' => 30,
                'category_id' => $kerajinanCategory->id ?? 1,
                'is_featured' => true,
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
                'image_path' => 'images/kotak4.jpg',
                'is_primary' => true,
            ],
        ]);

        $product3->images()->createMany([
            [
                'image_path' => 'images/kotak6.jpg',
                'is_primary' => true,
            ],
        ]);

        $product5->images()->createMany([
            [
                'image_path' => 'images/kotak5.jpg',
                'is_primary' => true,
            ],
        ]);

        $product6->images()->createMany([
            [
                'image_path' => 'images/kotak6.jpg',
                'is_primary' => true,
            ],
        ]);

        $product7->images()->createMany([
            [
                'image_path' => 'images/kotak7.jpg',
                'is_primary' => true,
            ],
        ]);

        $product8->images()->createMany([
            [
                'image_path' => 'images/gambar8.jpg',
                'is_primary' => true,
            ],
        ]);

        $product9->images()->createMany([
            [
                'image_path' => 'images/gambar9.jpg',
                'is_primary' => true,
            ],
        ]);

        $product10->images()->createMany([
            [
                'image_path' => 'images/gambar10.jpg',
                'is_primary' => true,
            ],
        ]);

        $product11->images()->createMany([
            [
                'image_path' => 'images/gambar11.jpg',
                'is_primary' => true,
            ],
        ]);

        $product12->images()->createMany([
            [
                'image_path' => 'images/gambar12.jpg',
                'is_primary' => true,
            ],
        ]);

        $product14->images()->createMany([
            [
                'image_path' => 'images/gambar14.jpg',
                'is_primary' => true,
            ],
        ]);

        $product15->images()->createMany([
            [
                'image_path' => 'images/gambar15.jpg',
                'is_primary' => true,
            ],
        ]);

        $product16->images()->createMany([
            [
                'image_path' => 'images/gambar16.jpg',
                'is_primary' => true,
            ],
        ]);

        $this->command->info('✅ Product images created');

        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed!');
        $this->command->info('📧 Login: admin@example.com / p');
    }
}