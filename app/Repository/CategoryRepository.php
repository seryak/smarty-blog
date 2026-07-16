<?php

declare(strict_types=1);

namespace App\Repository;

class CategoryRepository extends AbstractRepository
{
    protected const TABLE = 'categories';

    /**
     * @return list<array<string, mixed>>
     */
    public function hasArticles(): array
    {
        return $this->db->fetchAll(
            'SELECT DISTINCT c.* FROM categories c
             JOIN article_category ac ON ac.category_id = c.id',
        );
    }
}
