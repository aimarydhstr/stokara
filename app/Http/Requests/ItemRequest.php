<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9_-]+$/', Rule::unique('items')->ignore($this->route('item'))],
            'name' => ['required', 'string', 'max:120'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'unit' => ['required', Rule::in(['Pcs', 'Rim', 'Box', 'Pack', 'Botol', 'Roll', 'Set', 'Lusin'])],
            'minimum_stock' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'unit_price' => ['required', 'numeric', 'min:0', 'max:999999999999.99', 'decimal:0,2'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return ['sku' => 'kode barang', 'name' => 'nama barang', 'category_id' => 'kategori', 'unit' => 'satuan', 'minimum_stock' => 'stok minimum', 'unit_price' => 'harga satuan', 'description' => 'deskripsi'];
    }
}