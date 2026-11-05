<?php

// Creates only the dedicated Stokara databases/users. Existing Laragon databases are untouched.
$root = dirname(__DIR__);
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$exists = $pdo->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME IN ('stokara', 'stokara_testing')")->fetchColumn();
if ($exists) {
    throw new RuntimeException('Database stokara sudah ada. Konfigurasi manual diperlukan untuk menjaga data lama.');
}
$password = bin2hex(random_bytes(24));
$testPassword = bin2hex(random_bytes(24));
$adminPassword = bin2hex(random_bytes(10));
$pdo->exec('CREATE DATABASE `stokara` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$pdo->exec('CREATE DATABASE `stokara_testing` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
foreach ([['stokara_app', $password, 'stokara'], ['stokara_test', $testPassword, 'stokara_testing']] as [$user, $pass, $database]) {
    $pdo->exec('CREATE USER '.$pdo->quote($user)."@'127.0.0.1' IDENTIFIED BY ".$pdo->quote($pass));
    $pdo->exec("GRANT SELECT, INSERT, UPDATE, DELETE ON `{$database}`.* TO ".$pdo->quote($user)."@'127.0.0.1'");
}
$pdo->exec("GRANT ALL PRIVILEGES ON `stokara_testing`.* TO 'stokara_test'@'127.0.0.1'");
$base = file_get_contents($root.'/.env');
$settings = [
    'APP_NAME' => 'Stokara', 'APP_URL' => 'http://127.0.0.1:8000', 'APP_LOCALE' => 'id', 'APP_FALLBACK_LOCALE' => 'en',
    'APP_TIMEZONE' => 'Asia/Bangkok', 'DB_CONNECTION' => 'mysql', 'DB_HOST' => '127.0.0.1', 'DB_PORT' => '3306',
    'DB_DATABASE' => 'stokara', 'DB_USERNAME' => 'stokara_app', 'DB_PASSWORD' => $password,
    'SESSION_DRIVER' => 'file', 'CACHE_STORE' => 'file', 'QUEUE_CONNECTION' => 'sync',
    'STOKARA_ADMIN_EMAIL' => 'admin@stokara.test', 'STOKARA_ADMIN_PASSWORD' => $adminPassword,
];
foreach ($settings as $key => $value) {
    if (preg_match('/^'.preg_quote($key, '/').'=/m', $base)) {
        $base = preg_replace('/^'.preg_quote($key, '/').'=.*$/m', $key.'='.$value, $base);
    } else {
        $base .= "\n{$key}={$value}\n";
    }
}
file_put_contents($root.'/.env', $base);
$testing = $base;
foreach (['APP_ENV' => 'testing', 'DB_DATABASE' => 'stokara_testing', 'DB_USERNAME' => 'stokara_test', 'DB_PASSWORD' => $testPassword, 'CACHE_STORE' => 'array', 'SESSION_DRIVER' => 'array'] as $key => $value) {
    $testing = preg_replace('/^'.$key.'=.*$/m', $key.'='.$value, $testing);
}
file_put_contents($root.'/.env.testing', $testing);
echo "Database dan akun aplikasi dibuat. Kredensial tersimpan hanya di environment lokal.\n";