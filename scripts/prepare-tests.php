<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec("GRANT ALL PRIVILEGES ON `stokara_testing`.* TO 'stokara_test'@'127.0.0.1'");
echo "Akun pengujian dibatasi pada database stokara_testing.\n";