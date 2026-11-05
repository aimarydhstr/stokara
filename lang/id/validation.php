<?php

return [
    'required' => 'Kolom :attribute wajib diisi.',
    'string' => 'Kolom :attribute harus berupa teks.',
    'integer' => 'Kolom :attribute harus berupa bilangan bulat.',
    'numeric' => 'Kolom :attribute harus berupa angka.',
    'email' => 'Alamat email tidak valid.',
    'unique' => ':Attribute sudah digunakan atau transaksi sudah pernah disimpan.',
    'exists' => ':Attribute yang dipilih tidak ditemukan.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'uuid' => 'Identitas transaksi tidak valid. Muat ulang formulir.',
    'regex' => 'Format :attribute tidak valid.',
    'decimal' => ':Attribute maksimal memiliki :decimal angka desimal.',
    'date_format' => 'Format :attribute harus :format.',
    'before_or_equal' => ':Attribute tidak boleh setelah :date.',
    'after_or_equal' => ':Attribute tidak boleh sebelum :date.',
    'min' => ['numeric' => ':Attribute minimal :min.', 'string' => ':Attribute minimal :min karakter.'],
    'max' => ['numeric' => ':Attribute maksimal :max.', 'string' => ':Attribute maksimal :max karakter.'],
];