<?php

declare(strict_types=1);

namespace App\Repository;

class CategoryRepository extends AbstractRepository
{
    protected const TABLE = 'categories';
    private const ARTICLE_PIVOT_TABLE = 'article_category';

    /**
     * @return list<array<string, mixed>>
     */
    public function hasArticles(): array
    {
        $sql = strtr(
            <<<'SQL'
                SELECT DISTINCT c.*
                FROM {{table}} AS c
                INNER JOIN {{pivot}} AS ac ON ac.category_id = c.id
                SQL,
            [
                '{{table}}' => static::TABLE,
                '{{pivot}}' => self::ARTICLE_PIVOT_TABLE,
            ],
        );

        return $this->db->fetchAll($sql);
    }
}
