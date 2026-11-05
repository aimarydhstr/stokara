<?php
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
Artisan::command('stokara:admin', function () {
    $email = $this->ask('Email petugas', env('STOKARA_ADMIN_EMAIL', 'admin@stokara.test'));
    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('Alamat email tidak valid.');
        return 1;
    }
    $password = $this->secret('Kata sandi baru (minimal 12 karakter)');
    if (! is_string($password) || strlen($password) < 12) {
        $this->error('Kata sandi minimal 12 karakter.');
        return 1;
    }
    if ($password !== $this->secret('Ulangi kata sandi')) {
        $this->error('Konfirmasi kata sandi tidak sesuai.');
        return 1;
    }
    User::updateOrCreate(['email' => $email], ['name' => 'Admin Gudang', 'password' => Hash::make($password)]);
    $this->info('Akun petugas berhasil disimpan.');
})->purpose('Membuat atau memperbarui akun petugas dengan input kata sandi tersembunyi.');