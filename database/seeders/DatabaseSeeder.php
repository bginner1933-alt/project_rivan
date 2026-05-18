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
        $kerajinanCategory = Category::where('slug', 'Kerajinan')->first();
        $aksesorisCategory = Category::where('slug', 'aksesoris')->first();
        $souvenirCategory = Category::where('slug', 'souvenir')->first();

        // Produk 1
        $product1 = Product::updateOrCreate(
            ['slug' => 'Kayu-Box'],
            [
                'name' => 'Kotak Penyimpanan Kayu',
                'description' => 'Dari Kayu Premium',
                'price' => 50000,
                'rental_price' => null,
                'rental_unit' => 'day',
                'stock' => 10,
                'category_id' => $kerajinanCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        // Produk 2
        $product2 = Product::updateOrCreate(
            ['slug' => 'souvenir'],
            [
                'name' => 'Suvenir',
                'description' => 'Suvenir unik untuk acara pernikahan',
                'price' => null,
                'rental_price' => 50000,
                'rental_unit' => 'day',
                'stock' => 15,
                'category_id' => $souvenirCategory->id ?? 1,
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
                'rental_unit' => 'day',
                'stock' => 50,
                'category_id' => $souvenirCategory->id ?? 1,
                'is_featured' => false,
            ]
        );

        $product5 = Product::updateOrCreate(
            ['slug' => 'souvenir2'],
            [
                'name' => 'Souvenir',
                'description' => 'Souvenir unik untuk acara pernikahan',
                'price' => null,
                'rental_price' => 50000,
                'rental_unit' => 'day',
                'stock' => 30,
                'category_id' => $souvenirCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        $product6 = Product::updateOrCreate(
            ['slug' => 'souvenir3'],
            [
                'name' => 'Souvenir',
                'description' => 'Souvenir unik untuk acara pernikahan',
                'price' => null,
                'rental_price' => 50000,
                'rental_unit' => 'day',
                'stock' => 30,
                'category_id' => $souvenirCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        $product7 = Product::updateOrCreate(
            ['slug' => 'suvenir4'],
            [
                'name' => 'Souvenir',
                'description' => 'Souvenir unik untuk acara pernikahan',
                'price' => null,
                'rental_price' => 50000,
                'rental_unit' => 'day',
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        $product8 = Product::updateOrCreate(
            ['slug' => 'cincin1'],
            [
                'name' => 'Cincin Pernikahan',
                'description' => 'Cincin untuk acara pernikahan',
                'price' => 450000,
                'rental_price' => null,
                'rental_unit' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        $product9 = Product::updateOrCreate(
            ['slug' => 'cincin2'],
            [
                'name' => 'Cincin Pernikahan',
                'description' => 'Cincin untuk acara pernikahan',
                'price' => 450000,
                'rental_price' => null,
                'rental_unit' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        $product10 = Product::updateOrCreate(
            ['slug' => 'cincin3'],
            [
                'name' => 'Cincin Pernikahan',
                'description' => 'Cincin untuk acara pernikahan',
                'price' => 450000,
                'rental_price' => null,
                'rental_unit' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        $product11 = Product::updateOrCreate(
            ['slug' => 'cincin4'],
            [
                'name' => 'Cincin Pernikahan',
                'description' => 'Cincin untuk acara pernikahan',
                'price' => 450000,
                'rental_price' => null,
                'rental_unit' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        $product12 = Product::updateOrCreate(
            ['slug' => 'cincin5'],
            [
                'name' => 'Cincin Pernikahan',
                'description' => 'Cincin untuk acara pernikahan',
                'price' => 450000,
                'rental_price' => null,
                'rental_unit' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        $product14 = Product::updateOrCreate(
            ['slug' => 'cincin6'],
            [
                'name' => 'Cincin Pernikahan',
                'description' => 'Cincin untuk acara pernikahan',
                'price' => 450000,
                'rental_price' => null,
                'rental_unit' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        $product15 = Product::updateOrCreate(
            ['slug' => 'cincin7'],
            [
                'name' => 'Cincin Pernikahan',
                'description' => 'Cincin untuk acara pernikahan',
                'price' => 450000,
                'rental_price' => null,
                'rental_unit' => null,
                'stock' => 30,
                'category_id' => $aksesorisCategory->id ?? 1,
                'is_featured' => true,
            ]
        );

        $product16 = Product::updateOrCreate(
            ['slug' => 'wadah-kayu'],
            [
                'name' => 'Wadah Kayu',
                'description' => 'Wadah untuk menyimpan',
                'price' => 50000,
                'rental_price' => null,
                'rental_unit' => null,
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