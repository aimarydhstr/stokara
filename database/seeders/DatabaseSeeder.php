<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\Stock\StockIn;
use App\Services\Stock\StockOut;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('STOKARA_ADMIN_PASSWORD');
        if (! $password) {
            throw new \RuntimeException('Isi STOKARA_ADMIN_PASSWORD pada .env sebelum menjalankan seeder.');
        }
        $user = User::firstOrCreate(['email' => env('STOKARA_ADMIN_EMAIL', 'admin@stokara.test')], ['name' => 'Admin Gudang', 'password' => Hash::make($password)]);
        $groups = [
            ['Kertas & Buku', 'Kebutuhan cetak dan pencatatan kantor.', 'blue'],
            ['Alat Tulis', 'Perlengkapan menulis sehari-hari.', 'green'],
            ['Perlengkapan Meja', 'Kebutuhan meja kerja yang lebih tertata.', 'purple'],
            ['Arsip & Penyimpanan', 'Perlengkapan pengarsipan dokumen.', 'orange'],
            ['Tinta & Cetak', 'Tinta dan kebutuhan mesin cetak.', 'pink'],
            ['Perekat & Kemasan', 'Perekat, pembungkus, dan kemasan.', 'slate'],
        ];
        $categories = [];
        foreach ($groups as [$name, $description, $color]) {
            $categories[] = Category::firstOrCreate(['name' => $name], compact('description', 'color'));
        }
        $catalog = [
            ['ATK-001', 'Kertas A4 80 gsm', 0, 'Rim', 15, 58000, 82],
            ['ATK-002', 'Buku Catatan A5', 0, 'Pcs', 10, 18500, 45],
            ['ATK-003', 'Kertas HVS F4', 0, 'Rim', 10, 62000, 8],
            ['ATK-004', 'Pulpen Gel Hitam', 1, 'Box', 10, 42000, 36],
            ['ATK-005', 'Pensil 2B', 1, 'Box', 8, 28000, 24],
            ['ATK-006', 'Spidol Whiteboard', 1, 'Pcs', 12, 9500, 6],
            ['ATK-007', 'Stabilo Pastel', 1, 'Set', 5, 45000, 18],
            ['ATK-008', 'Stapler Medium', 2, 'Pcs', 5, 32000, 17],
            ['ATK-009', 'Gunting Stainless', 2, 'Pcs', 5, 22000, 14],
            ['ATK-010', 'Penggaris Besi 30 cm', 2, 'Pcs', 5, 12000, 23],
            ['ATK-011', 'Map Kancing A4', 3, 'Pack', 10, 25000, 32],
            ['ATK-012', 'Ordner A4', 3, 'Pcs', 8, 38000, 28],
            ['ATK-013', 'Document Tray', 3, 'Set', 4, 85000, 11],
            ['ATK-014', 'Tinta Printer Hitam', 4, 'Botol', 8, 95000, 3],
            ['ATK-015', 'Tinta Printer Warna', 4, 'Set', 5, 265000, 12],
            ['ATK-016', 'Lakban Bening', 5, 'Roll', 10, 14000, 40],
            ['ATK-017', 'Lem Kertas', 5, 'Pcs', 8, 7500, 21],
            ['ATK-018', 'Double Tape 24 mm', 5, 'Roll', 6, 11000, 0],
        ];
        $inventory = app(InventoryService::class);
        foreach ($catalog as $index => [$sku, $name, $group, $unit, $minimum, $price, $target]) {
            if (Item::where('sku', $sku)->exists()) {
                continue;
            }
            $item = Item::create(['sku' => $sku, 'name' => $name, 'category_id' => $categories[$group]->id, 'unit' => $unit, 'minimum_stock' => $minimum, 'unit_price' => $price, 'description' => 'Persediaan '.$name.' untuk operasional kantor.']);
            $initial = $inventory->record(new StockIn, $item->id, $target + 40, $user, ['request_token' => (string) Str::uuid(), 'occurred_at' => today()->subDays(14)->toDateString(), 'notes' => 'Penerimaan persediaan awal']);
            $initial->forceFill(['created_at' => today()->subDays(14)->setTime(9, 0), 'updated_at' => today()->subDays(14)->setTime(9, 0)])->save();
            for ($day = 13; $day >= 0; $day--) {
                $quantity = 2 + (($index + $day) % 5);
                $data = ['request_token' => (string) Str::uuid(), 'occurred_at' => today()->subDays($day)->toDateString()];
                $incoming = $inventory->record(new StockIn, $item->id, $quantity, $user, $data + ['notes' => 'Penerimaan barang dari pemasok']);
                $incoming->forceFill(['created_at' => today()->subDays($day)->setTime(9, $index % 60), 'updated_at' => today()->subDays($day)->setTime(9, $index % 60)])->save();
                $data['request_token'] = (string) Str::uuid();
                $outgoing = $inventory->record(new StockOut, $item->id, $quantity + ($day <= 7 ? 3 : 2), $user, $data + ['notes' => 'Distribusi kebutuhan operasional kantor']);
                $outgoing->forceFill(['created_at' => today()->subDays($day)->setTime(14, $index % 60), 'updated_at' => today()->subDays($day)->setTime(14, $index % 60)])->save();
            }
            // 8 days x 3 + 6 days x 2 = 36; finish with the remaining four units.
            $inventory->record(new StockOut, $item->id, 4, $user, ['request_token' => (string) Str::uuid(), 'occurred_at' => today()->toDateString(), 'notes' => 'Pemakaian kebutuhan administrasi']);
        }
    }
}