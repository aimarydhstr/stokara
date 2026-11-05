<?php

require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (! app()->environment('local') || config('database.connections.mysql.database') !== 'stokara') {
    throw new RuntimeException('Migration helper hanya untuk database lokal stokara.');
}
// Use Laragon administrator only for schema changes; the web app keeps DML-only permissions.
config(['database.connections.mysql.username' => 'root', 'database.connections.mysql.password' => '']);
Illuminate\Support\Facades\DB::purge('mysql');
$status = Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
echo Illuminate\Support\Facades\Artisan::output();
exit($status);