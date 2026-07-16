<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Database;

abstract class AbstractRepository
{
    protected const TABLE = '';

    public function __construct(protected readonly Database $db)
    {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        return $this->db->fetchOne(
            sprintf('SELECT * FROM %s WHERE id = :id', static::TABLE),
            ['id' => $id],
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(static fn (string $column): string => ':' . $column, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            static::TABLE,
            implode(', ', $columns),
            implode(', ', $placeholders),
        );

        $this->db->execute($sql, $data);

        return (int) $this->db->lastInsertId();
    }
}
