<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core;

use App\Core\Database;
use PDO;
use PDOStatement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(Database::class)]
final class DatabaseTest extends TestCase
{
    private PDO&MockObject $pdo;
    private PDOStatement&MockObject $statement;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->statement = $this->createMock(PDOStatement::class);
    }

    #[TestDox('fetchAll() готовит запрос, выполняет его с параметрами и возвращает все строки')]
    public function test_fetch_all_returns_all_rows(): void
    {
        $rows = [['id' => 1], ['id' => 2]];

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM articles')
            ->willReturn($this->statement);

        $this->statement->expects($this->once())
            ->method('execute')
            ->with(['status' => 'published']);

        $this->statement->method('fetchAll')->willReturn($rows);

        $database = new Database($this->pdo);

        $result = $database->fetchAll('SELECT * FROM articles', ['status' => 'published']);

        self::assertSame($rows, $result);
    }

    #[TestDox('fetchOne() возвращает первую найденную строку')]
    public function test_fetch_one_returns_row_when_found(): void
    {
        $row = ['id' => 1, 'title' => 'Hello'];

        $this->pdo->method('prepare')->willReturn($this->statement);
        $this->statement->expects($this->once())
            ->method('execute')
            ->with(['id' => 1]);
        $this->statement->method('fetch')->willReturn($row);

        $database = new Database($this->pdo);

        $result = $database->fetchOne('SELECT * FROM articles WHERE id = :id', ['id' => 1]);

        self::assertSame($row, $result);
    }

    #[TestDox('fetchOne() возвращает null, если строка не найдена')]
    public function test_fetch_one_returns_null_when_not_found(): void
    {
        $this->pdo->method('prepare')->willReturn($this->statement);
        $this->statement->method('execute');
        $this->statement->method('fetch')->willReturn(false);

        $database = new Database($this->pdo);

        $result = $database->fetchOne('SELECT * FROM articles WHERE id = :id', ['id' => 999]);

        self::assertNull($result);
    }

    #[TestDox('execute() возвращает количество затронутых строк')]
    public function test_execute_returns_affected_row_count(): void
    {
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM articles WHERE id = :id')
            ->willReturn($this->statement);

        $this->statement->expects($this->once())
            ->method('execute')
            ->with(['id' => 1]);

        $this->statement->method('rowCount')->willReturn(1);

        $database = new Database($this->pdo);

        $result = $database->execute('DELETE FROM articles WHERE id = :id', ['id' => 1]);

        self::assertSame(1, $result);
    }

    #[TestDox('Методы без параметров вызывают execute() с пустым массивом')]
    public function test_methods_default_to_empty_params(): void
    {
        $this->pdo->method('prepare')->willReturn($this->statement);
        $this->statement->expects($this->once())
            ->method('execute')
            ->with([]);
        $this->statement->method('fetchAll')->willReturn([]);

        (new Database($this->pdo))->fetchAll('SELECT * FROM articles');
    }
}
