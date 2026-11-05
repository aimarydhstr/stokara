# Rancangan presentasi Stokara — maksimal tujuh slide

Durasi penyajian 15 menit, dilanjutkan tanya jawab 15 menit sesuai dokumen. Gunakan screenshot dari folder screenshots dan diagram dalam ASESMEN.md. Isikan hasil pengujian aktual, bukan angka perkiraan.

| Slide | Isi | Waktu |
|---|---|---|
| 1 | Judul, masalah pencatatan stok, tujuan, batas satu gudang/satu peran | 1 menit |
| 2 | Alur petugas dan fitur: data barang, masuk, keluar, riwayat, dashboard | 1 menit |
| 3 | Class diagram, interface, inheritance, polymorphism; beda output masuk/keluar; property overloading PHP dan pemeriksaan sintaks | 3 menit |
| 4 | Laravel/Eloquent, Carbon, Bootstrap: fungsi, lisensi, efisiensi reuse, dependensi/lockfile, integrasi dan prosedur update | 2 menit |
| 5 | ERD, query, indeks, koneksi, hak akses database, transaksi dan row lock | 2 menit |
| 6 | Demo singkat alur barang masuk/keluar, penolakan stok kurang, filter riwayat | 4 menit |
| 7 | Bukti tes fungsi, browser/mobile, concurrency, dan EXPLAIN/benchmark; batas implementasi | 2 menit |

## Skenario demo

1. Login dan perlihatkan dashboard.
2. Buat barang khusus demo, misalnya Kertas Memo Demo, dengan kategori/satuan yang sesuai. Stok awal nol.
3. Catat barang masuk 20. Tunjukkan stok 20 dan satu riwayat.
4. Catat barang keluar 5. Tunjukkan stok 15 beserta nilai sebelum/sesudah.
5. Coba keluarkan 30. Tunjukkan validasi; stok tetap 15.
6. Cari barang tersebut di riwayat dan filter jenis keluar; unduh CSV bila waktu cukup.
7. Tunjukkan kontrak StockOperation dan dua implementasinya; siapkan file agar perpindahan dari demo ke kode cepat.

## Bukti yang dibawa

- Kode sumber dan dua lockfile, class diagram serta ERD.
- Dokumentasi komponen, lisensi, query, dan hak akses.
- Hasil `php artisan test`, `concurrency-results.json`, dan `performance-results.json`.
- Screenshot desktop/mobile dan `browser-results.json`.
- PPT maksimal tujuh slide yang disusun dari rancangan ini.

Sebelum presentasi: pastikan MySQL aktif, server lokal berjalan, browser sudah siap, dan akun dapat login. Aset tampilan bersifat lokal. Jangan tampilkan .env atau kata sandi saat berbagi layar.