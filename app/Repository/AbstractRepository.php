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
}
