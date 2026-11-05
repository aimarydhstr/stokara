<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\Stock\StockIn;
use App\Services\Stock\StockOut;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->loadEnvironmentFrom('.env.testing');
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (config('database.default') !== 'mysql' || config('database.connections.mysql.database') !== 'stokara_testing') {
    throw new RuntimeException('Integration test hanya boleh berjalan pada stokara_testing.');
}
if (($argv[1] ?? '') === 'worker') {
    echo "ready\n";
    flush();
    try {
        app(InventoryService::class)->record(new StockOut, (int) $argv[2], 7, User::findOrFail($argv[3]), ['request_token' => (string) Str::uuid(), 'occurred_at' => today()->toDateString(), 'notes' => 'Concurrent integration test']);
        echo "success\n";
    } catch (ValidationException) {
        echo "rejected\n";
    }
    exit;
}
$category = Category::create(['name' => 'Concurrency '.Str::random(12), 'color' => 'green']);
$user = User::factory()->create();
$item = Item::create(['category_id' => $category->id, 'sku' => 'CONC-'.Str::random(10), 'name' => 'Concurrent test item', 'unit' => 'Pcs', 'minimum_stock' => 0, 'unit_price' => 0]);
try {
    app(InventoryService::class)->record(new StockIn, $item->id, 10, $user, ['request_token' => (string) Str::uuid(), 'occurred_at' => today()->toDateString()]);
    DB::beginTransaction();
    Item::whereKey($item->id)->lockForUpdate()->firstOrFail();
    $workers = [];
    for ($i = 0; $i < 2; $i++) {
        $worker = new Process([PHP_BINARY, __FILE__, 'worker', (string) $item->id, (string) $user->id], $root);
        $worker->setTimeout(25);
        $worker->start();
        $worker->waitUntil(fn ($type, $buffer) => str_contains($buffer, 'ready'));
        $workers[] = $worker;
    }
    DB::commit();
    $outcomes = [];
    foreach ($workers as $worker) {
        $worker->wait();
        if (! $worker->isSuccessful()) throw new RuntimeException('Concurrent worker gagal.');
        $outcomes[] = str_contains($worker->getOutput(), 'success') ? 'success' : 'rejected';
    }
    $remaining = $item->fresh()->stock;
    $outgoing = $item->movements()->where('type', 'out')->count();
    $passed = $remaining === 3 && $outgoing === 1 && count(array_filter($outcomes, fn ($result) => $result === 'success')) === 1;
    $result = ['database' => 'stokara_testing', 'initial_stock' => 10, 'simultaneous_requests' => [7, 7], 'outcomes' => $outcomes, 'final_stock' => $remaining, 'outgoing_records' => $outgoing, 'passed' => $passed];
    file_put_contents($root.'/docs/concurrency-results.json', json_encode($result, JSON_PRETTY_PRINT));
    echo json_encode($result, JSON_PRETTY_PRINT).PHP_EOL;
    if (! $passed) throw new RuntimeException('Concurrency invariant failed.');
} finally {
    if (DB::transactionLevel() > 0) DB::rollBack();
    $item->movements()->delete();
    $item->delete();
    $category->delete();
    $user->delete();
}