<?php

declare(strict_types=1);

namespace App\Repository;

class ArticleRepository extends AbstractRepository
{
    protected const TABLE = 'articles';

    /**
     * @return list<array<string, mixed>>
     */
    public function latest(int $limit = 5): array
    {
        return $this->db->fetchAll(
            sprintf('SELECT * FROM %s ORDER BY published_at DESC LIMIT :limit', static::TABLE),
            ['limit' => $limit],
        );
    }
}
