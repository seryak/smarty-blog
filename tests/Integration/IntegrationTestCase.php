<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Core\Database;
use PDO;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected PDO $pdo;
    protected Database $db;

    public static function setUpBeforeClass(): void
    {
        $server = self::tryConnect(withDatabase: false);
        if ($server === null) {
            return;
        }

        $dbName = self::config()['dbname'];
        $server->exec("DROP DATABASE IF EXISTS `{$dbName}`");
        $server->exec("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $server->exec("USE `{$dbName}`");

        $schema = file_get_contents(dirname(__DIR__, 2) . '/bin/db.sql');
        if ($schema === false) {
            throw new \RuntimeException('Unable to read bin/db.sql');
        }

        foreach (array_filter(array_map('trim', explode(';', $schema))) as $statement) {
            $server->exec($statement);
        }
    }

    protected function setUp(): void
    {
        $pdo = self::tryConnect(withDatabase: true);
        if ($pdo === null) {
            self::markTestSkipped('MySQL недоступен — интеграционные тесты пропущены.');
        }

        $this->pdo = $pdo;
        $this->pdo->beginTransaction();
        $this->db = new Database($this->pdo);
    }

    protected function tearDown(): void
    {
        if (isset($this->pdo) && $this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function insertArticle(array $data = []): int
    {
        $article = array_merge([
            'title' => 'Title',
            'description' => 'Description',
            'text' => 'Text',
            'image' => 'https://example.test/image.jpg',
            'views' => 0,
            'published_at' => '2026-01-01 00:00:00',
        ], $data);

        $this->db->execute(
            'INSERT INTO articles (title, description, `text`, image, views, published_at)
             VALUES (:title, :description, :text, :image, :views, :published_at)',
            $article,
        );

        return (int) $this->db->lastInsertId();
    }

    protected function insertCategory(string $title = 'Category', string $description = 'Description'): int
    {
        $this->db->execute(
            'INSERT INTO categories (title, description) VALUES (:title, :description)',
            ['title' => $title, 'description' => $description],
        );

        return (int) $this->db->lastInsertId();
    }

    protected function linkArticleCategory(int $articleId, int $categoryId): void
    {
        $this->db->execute(
            'INSERT INTO article_category (article_id, category_id) VALUES (:article_id, :category_id)',
            ['article_id' => $articleId, 'category_id' => $categoryId],
        );
    }

    /**
     * @return array{host: string, port: int, dbname: string, user: string, password: string}
     */
    private static function config(): array
    {
        return [
            'host' => getenv('DB_TEST_HOST') ?: '127.0.0.1',
            'port' => (int) (getenv('DB_TEST_PORT') ?: 3306),
            'dbname' => getenv('DB_TEST_NAME') ?: 'smarty_blog_test',
            'user' => getenv('DB_TEST_USER') ?: 'root',
            'password' => getenv('DB_TEST_PASSWORD') ?: 'root',
        ];
    }

    private static function tryConnect(bool $withDatabase): ?PDO
    {
        $config = self::config();

        $dsn = $withDatabase
            ? sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $config['host'], $config['port'], $config['dbname'])
            : sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $config['host'], $config['port']);

        try {
            return new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException) {
            return null;
        }
    }
}
