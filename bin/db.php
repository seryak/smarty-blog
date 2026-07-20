<?php

declare(strict_types=1);

use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$config = require dirname(__DIR__) . '/config/database.php';

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $config['host'], $config['port']),
    $config['user'],
    $config['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);

$dbName = $config['dbname'];

echo "Dropping database `{$dbName}` if it exists...\n";
$pdo->exec("DROP DATABASE IF EXISTS `{$dbName}`");

echo "Creating database `{$dbName}`...\n";
$pdo->exec("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `{$dbName}`");

$sql = file_get_contents(__DIR__ . '/db.sql');
if ($sql === false) {
    throw new RuntimeException('Unable to read bin/db.sql');
}

echo "Applying schema...\n";
foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
    $pdo->exec($statement);
}

echo "Done.\n";
