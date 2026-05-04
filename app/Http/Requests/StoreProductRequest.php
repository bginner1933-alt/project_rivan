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
            'price' => ['required', 'numeric', 'min:0'],
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
        ];
    }

    public function messages(): array
    {
        return [
            'rental_unit.required_with' =>
                'Jika harga sewa diisi, satuan sewa wajib dipilih.',

            'rental_unit.in' =>
                'Satuan sewa tidak valid.',

            'discount_price.lt' =>
                'Harga diskon harus lebih rendah dari harga asli.',
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