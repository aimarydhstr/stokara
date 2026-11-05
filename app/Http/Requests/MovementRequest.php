<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['in', 'out'])],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'occurred_at' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:500'],
            'request_token' => ['required', 'uuid', 'unique:stock_movements,request_token'],
        ];
    }

    public function messages(): array
    {
        return ['occurred_at.before_or_equal' => 'Tanggal transaksi tidak boleh melewati hari ini.', 'request_token.unique' => 'Transaksi ini sudah tersimpan. Buka formulir baru untuk transaksi berikutnya.'];
    }

    public function attributes(): array
    {
        return ['type' => 'jenis transaksi', 'item_id' => 'barang', 'quantity' => 'jumlah', 'occurred_at' => 'tanggal transaksi', 'notes' => 'keterangan', 'request_token' => 'identitas transaksi'];
    }
}