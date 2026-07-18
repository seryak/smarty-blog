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
        $sql = strtr(
            <<<'SQL'
                SELECT *
                FROM {{table}}
                WHERE id = :id
                SQL,
            ['{{table}}' => static::TABLE],
        );

        return $this->db->fetchOne($sql, ['id' => $id]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(static fn (string $column): string => ':' . $column, $columns);

        $sql = strtr(
            <<<'SQL'
                INSERT INTO {{table}} ({{columns}})
                VALUES ({{placeholders}})
                SQL,
            [
                '{{table}}' => static::TABLE,
                '{{columns}}' => implode(', ', $columns),
                '{{placeholders}}' => implode(', ', $placeholders),
            ],
        );

        $this->db->execute($sql, $data);

        return (int) $this->db->lastInsertId();
    }
}
