# Stokara — Sistem Inventori Gudang ATK

Aplikasi inventori Laravel 13, Blade, Bootstrap 5, dan **MySQL Laragon**. Satu gudang, satu jenis pengguna petugas, dan satu barang per transaksi. Aset tampilan disimpan lokal; aplikasi tidak memerlukan CDN, Vite, atau layanan eksternal saat digunakan.

## Menjalankan proyek yang sudah disiapkan

1. Pastikan MySQL Laragon aktif pada port 3306.
2. Buka terminal:

```powershell
cd D:\asesor\stokara
php artisan serve --host=127.0.0.1 --port=8000
```

3. Buka **http://127.0.0.1:8000**. Alternatif: klik dua kali `start.bat`.
4. Akun awal: `admin@stokara.test`. Kata sandi dibuat acak saat setup dan hanya disimpan pada `STOKARA_ADMIN_PASSWORD` dalam `.env` lokal. Jangan membagikan file tersebut.

Untuk menetapkan kata sandi yang mudah Anda ingat, jalankan perintah berikut. Input kata sandi disembunyikan:

```powershell
php artisan stokara:admin
```

Perintah ini mengubah akun database, bukan nilai kata sandi awal dalam `.env`. Setelah diganti, gunakan kata sandi baru untuk login.

## Fitur

- Login, pembatasan percobaan login, session regeneration, dan logout.
- CRUD kategori dengan pilihan warna dan validasi.
- CRUD barang, SKU unik, satuan, harga, batas stok minimum, dan detail barang.
- Barang baru dimulai dari stok nol. Perubahan stok hanya melalui transaksi.
- Barang masuk dan keluar dengan pratinjau stok akhir.
- Pencegahan stok negatif melalui transaksi database dan `lockForUpdate()`.
- Token unik untuk mencegah penerapan transaksi dua kali.
- Riwayat memuat petugas, tanggal, keterangan, stok sebelum/sesudah, dan referensi.
- Pencarian, filter tanggal/jenis/kategori/status, pagination, dan ekspor CSV.
- Dashboard, grafik periode 7/14/30 hari, kategori, estimasi nilai, dan stok minimum.
- Sidebar responsif, panduan, empty state, feedback validasi, dan loading state.
- Barang yang memiliki stok/riwayat serta kategori yang digunakan dilindungi dari penghapusan.

## Data dan konfigurasi lokal

- Database aplikasi: `stokara`.
- Database pengujian: `stokara_testing`.
- Akun web `stokara_app` hanya memiliki SELECT/INSERT/UPDATE/DELETE pada `stokara`.
- Akun `stokara_test` memiliki izin schema hanya pada `stokara_testing`.
- Kredensial tersimpan dalam `.env` dan `.env.testing`, keduanya diabaikan Git.
- Data demo: 18 barang, 6 kategori, dan riwayat stok dengan nilai yang konsisten.
- Angka masuk/keluar agregat adalah penjumlahan kuantitas lintas satuan. Baca per barang untuk rincian satuan.
- Tanggal transaksi adalah tanggal bisnis. Riwayat stok sebelum/sesudah mengikuti urutan pencatatan, termasuk jika tanggal bisnis diisi mundur.

Schema diubah melalui akun administratif, bukan akun web:

```powershell
php scripts/migrate-local.php
```

Helper tersebut khusus database lokal `stokara` dan konfigurasi default administrator Laragon. Database lain tidak diubah. Setup awal pada mesin baru, setelah dependensi tersedia: salin `.env.example` ke `.env`, buat application key, lalu jalankan `scripts/setup-local.php`, migration helper, dan seeder. Setup menolak database Stokara yang sudah ada; jangan menjalankannya untuk mengulang demo.

## Struktur utama

```text
app/Contracts/StockOperation.php             kontrak operasi stok
app/Services/Stock/                          inheritance dan implementasi masuk/keluar
app/Services/InventoryService.php            transaksi, row lock, dan audit history
app/Models/                                 model dan relasi
app/Http/Controllers/                        autentikasi dan alur aplikasi
app/Http/Requests/                           validasi input
resources/views/                            halaman Blade dan komponen
public/css/app.css                          desain panel dan responsivitas
public/js/app.js                            interaksi dan pratinjau
scripts/                                   setup lokal, benchmark, pemeriksaan sintaks
```

## Pengujian

Semua feature/integration test menggunakan **MySQL**, dengan database terpisah. `phpunit.xml` menunjuk `stokara_testing`; kredensial berasal dari `.env.testing`. Jangan mengganti database test ke database aplikasi.

```powershell
php artisan test
php tests/integration/concurrency.php
php scripts/benchmark.php
php artisan view:cache
php scripts/check-syntax.php
```

Browser QA memerlukan server aplikasi berjalan, Node.js, dan Chrome lokal:

```powershell
npm install
npm run build
npm run test:browser
```

Browser test menggunakan kredensial awal `.env`; jika kata sandi petugas telah diganti melalui CLI, sesuaikan kredensial pengujian lokal. Pengujian browser tidak mencatat transaksi baru pada database aplikasi.

Bukti tersimpan pada `docs/browser-results.json`, `docs/concurrency-results.json`, `docs/performance-results.json`, dan `docs/screenshots/`. Benchmark memakai 20.000 data sintetis pada database pengujian dan melakukan rollback setelah selesai.

## Dokumentasi asesmen

Baca [docs/ASESMEN.md](docs/ASESMEN.md) untuk spesifikasi, algoritma, class diagram, ERD, pemetaan OOP, library/lisensi, query, keamanan, serta jawaban pertanyaan lisan. [docs/PRESENTASI.md](docs/PRESENTASI.md) berisi susunan maksimal tujuh slide dan skenario demo.

## Batas implementasi

Tidak ada multi-gudang, pembelian/penjualan, integrasi pembayaran, atau inventaris per nomor seri. Riwayat bersifat permanen pada antarmuka; koreksi operasional dicatat melalui transaksi lawan dengan keterangan yang jelas. Ekspor CSV memuat hasil filter saat ini. Akun aplikasi tetap satu tingkat petugas.