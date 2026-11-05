<?php
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Category;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
$inconsistent = DB::select("SELECT items.id, items.stock, COALESCE(SUM(CASE WHEN stock_movements.type = 'in' THEN stock_movements.quantity ELSE -CAST(stock_movements.quantity AS SIGNED) END), 0) AS ledger_stock FROM items LEFT JOIN stock_movements ON items.id = stock_movements.item_id GROUP BY items.id, items.stock HAVING items.stock <> ledger_stock");
$report = ['database' => DB::connection()->getDatabaseName(), 'mysql_version' => DB::selectOne('SELECT VERSION() AS version')->version, 'items' => Item::count(), 'categories' => Category::count(), 'movements' => StockMovement::count(), 'ledger_consistent' => count($inconsistent) === 0, 'inconsistent_items' => $inconsistent, 'application_permissions' => array_map(fn ($row) => array_values((array) $row)[0], DB::select('SHOW GRANTS FOR CURRENT_USER()'))];
file_put_contents(dirname(__DIR__).'/docs/system-check.json', json_encode($report, JSON_PRETTY_PRINT));
echo json_encode($report, JSON_PRETTY_PRINT).PHP_EOL;
exit($report['ledger_consistent'] ? 0 : 1);