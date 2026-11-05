<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->loadEnvironmentFrom('.env.testing');
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (config('database.default') !== 'mysql' || config('database.connections.mysql.database') !== 'stokara_testing') {
    throw new RuntimeException('Benchmark hanya boleh berjalan pada stokara_testing.');
}
DB::beginTransaction();
try {
    $token = Str::random(8);
    $category = Category::create(['name' => 'Benchmark '.$token, 'color' => 'green']);
    $user = User::factory()->create();
    $item = Item::create(['category_id' => $category->id, 'sku' => 'BENCH-'.$token, 'name' => 'Synthetic benchmark item', 'unit' => 'Pcs', 'minimum_stock' => 0, 'unit_price' => 0]);
    $now = now()->format('Y-m-d H:i:s');
    for ($chunk = 0; $chunk < 20; $chunk++) {
        $rows = [];
        for ($offset = 0; $offset < 1000; $offset++) {
            $i = $chunk * 1000 + $offset;
            $incoming = $i % 2 === 0;
            $rows[] = ['reference' => 'BENCH-'.$token.'-'.$i, 'request_token' => (string) Str::uuid(), 'item_id' => $item->id, 'user_id' => $user->id, 'type' => $incoming ? 'in' : 'out', 'quantity' => 1, 'stock_before' => $incoming ? 0 : 1, 'stock_after' => $incoming ? 1 : 0, 'occurred_at' => today()->subDays((int) floor($i / 2) % 365)->toDateString(), 'created_at' => $now, 'updated_at' => $now];
        }
        DB::table('stock_movements')->insert($rows);
    }
    $where = " WHERE type = ? AND occurred_at >= ? AND occurred_at <= ?";
    $queries = ['full_scan' => 'SELECT SUM(quantity) AS total FROM stock_movements USE INDEX ()'.$where, 'indexed' => 'SELECT SUM(quantity) AS total FROM stock_movements'.$where];
    $params = ['out', today()->subDays(2)->toDateString(), today()->toDateString()];
    $results = [];
    foreach ($queries as $name => $query) {
        DB::select($query, $params);
        $durations = [];
        for ($round = 0; $round < 7; $round++) {
            $start = hrtime(true);
            $value = DB::select($query, $params)[0]->total;
            $durations[] = (hrtime(true) - $start) / 1000000;
        }
        sort($durations);
        $plan = DB::select('EXPLAIN '.$query, $params)[0];
        $results[$name] = ['median_ms' => round($durations[3], 3), 'result' => (int) $value, 'access_type' => $plan->type, 'chosen_index' => $plan->key, 'estimated_rows' => $plan->rows];
    }
    $report = ['mysql_version' => DB::selectOne('SELECT VERSION() AS version')->version, 'database' => 'stokara_testing', 'synthetic_rows' => 20000, 'rounds_after_warmup' => 7, 'query' => $queries['indexed'], 'parameters' => $params, 'results' => $results, 'note' => 'Baseline uses USE INDEX () to demonstrate a full scan. Indexed query lets the optimizer choose. Results are local measurements, not performance guarantees. Synthetic data is rolled back.'];
    file_put_contents($root.'/docs/performance-results.json', json_encode($report, JSON_PRETTY_PRINT));
    echo json_encode($report, JSON_PRETTY_PRINT).PHP_EOL;
} finally {
    DB::rollBack();
}