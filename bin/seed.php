<?php

declare(strict_types=1);

use App\Core\Database;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$config = require dirname(__DIR__) . '/config/database.php';

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $config['host'], $config['port'], $config['dbname']),
    $config['user'],
    $config['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);

$db = new Database($pdo);
$articles = new ArticleRepository($db);
$categories = new CategoryRepository($db);

// Детерминированный рандом — раскладка статей по категориям воспроизводима между запусками
mt_srand(42);

$lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor '
    . 'incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud '
    . 'exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';

// 6 категорий
$categoryIds = [];
for ($i = 1; $i <= 6; $i++) {
    $id = $categories->insert([
        'title' => "category {$i}",
        'description' => $lorem,
    ]);
    $categoryIds[] = $id;
    echo "Created category #{$id}: category {$i}\n";
}

// 25 статей: "Статья N", Lorem-описание/текст, случайные просмотры и дата
$articlesCount = 25;
for ($i = 1; $i <= $articlesCount; $i++) {
    $articleId = $articles->insert([
        'title' => "Статья {$i}",
        'description' => $lorem,
        'text' => $lorem . ' ' . $lorem,
        'image' => 'https://picsum.photos/300/200',
        'views' => mt_rand(0, 500),
        'published_at' => sprintf('2026-%02d-%02d %02d:%02d:00', mt_rand(1, 12), mt_rand(1, 28), mt_rand(0, 23), mt_rand(0, 59)),
    ]);
    echo "Created article #{$articleId}: Статья {$i}\n";

    // Случайно назначаем 1–3 уникальные категории — для разных тест-кейсов
    $pickedIndexes = (array) array_rand($categoryIds, mt_rand(1, 3));
    foreach ($pickedIndexes as $index) {
        $categoryId = $categoryIds[$index];
        $db->execute(
            'INSERT INTO article_category (article_id, category_id) VALUES (:article_id, :category_id)',
            ['article_id' => $articleId, 'category_id' => $categoryId],
        );
        echo "  linked to category #{$categoryId}\n";
    }
}

echo "Done.\n";
