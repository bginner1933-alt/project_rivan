<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            // =========================
            // PRODUCT BASIC
            // =========================
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            // =========================
            // PRICE
            // =========================
            'price' => ['nullable', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],

            // =========================
            // RENTAL (SEWA)
            // =========================
            'rental_price' => ['nullable', 'numeric', 'min:0'],
            'rental_unit' => [
                'nullable',
                'required_with:rental_price',
                'in:hour,day,week,month'
            ],

            // =========================
            // STOCK & OTHER
            // =========================
            'stock' => ['required', 'integer', 'min:0'],
            'weight' => ['required', 'integer', 'min:1'],

            // =========================
            // FLAGS
            // =========================
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],

            // =========================
            // IMAGES
            // =========================
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,png,webp,jpeg', 'max:2048'],

            'price.required_without' =>
                'Isi harga jual atau harga sewa.',

            'rental_price.required_without' =>
                'Isi harga jual atau harga sewa.',
        ];
    }

    public function messages(): array
    {
        return [

            // =========================
            // CATEGORY
            // =========================
            'category_id.required' =>
                'Pilih kategori dulu.',

            'category_id.exists' =>
                'Kategori tidak ditemukan.',

            // =========================
            // NAME
            // =========================
            'name.required' =>
                'Nama produk wajib diisi.',

            'name.max' =>
                'Nama produk terlalu panjang.',

            // =========================
            // PRICE
            // =========================
            'price.required' =>
                'Harga produk wajib diisi.',

            'price.numeric' =>
                'Harga harus berupa angka.',

            'price.min' =>
                'Harga tidak boleh minus.',

            // =========================
            // DISCOUNT
            // =========================
            'discount_price.numeric' =>
                'Harga diskon harus berupa angka.',

            'discount_price.lt' =>
                'Harga diskon harus lebih rendah dari harga asli.',

            // =========================
            // RENTAL
            // =========================
            'rental_price.numeric' =>
                'Harga sewa harus berupa angka.',

            'rental_unit.required_with' =>
                'Jika harga sewa diisi, satuan sewa wajib dipilih.',

            'rental_unit.in' =>
                'Satuan sewa tidak valid.',

            // =========================
            // STOCK
            // =========================
            'stock.required' =>
                'Stok produk wajib diisi.',

            'stock.integer' =>
                'Stok harus berupa angka.',

            'stock.min' =>
                'Stok tidak boleh minus.',

            // =========================
            // WEIGHT
            // =========================
            'weight.required' =>
                'Berat produk wajib diisi.',

            'weight.integer' =>
                'Berat harus berupa angka.',

            'weight.min' =>
                'Berat minimal 1 gram.',

            // =========================
            // IMAGES
            // =========================
            'images.array' =>
                'Format gambar tidak valid.',

            'images.max' =>
                'Maksimal upload 10 gambar.',

            'images.*.image' =>
                'File harus berupa gambar.',

            'images.*.mimes' =>
                'Format gambar harus JPG, PNG, JPEG, atau WEBP.',

            'images.*.max' =>
                'Ukuran gambar maksimal 2MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'is_featured' => $this->boolean('is_featured', false),
        ]);
    }
}