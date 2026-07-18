<?php

declare(strict_types=1);

namespace App\Repository;

use App\Enum\ArticleSort;
use App\Enum\SortDirection;

class ArticleRepository extends AbstractRepository
{
    protected const TABLE = 'articles';
    private const CATEGORY_PIVOT_TABLE = 'article_category';
    private const CATEGORIES_TABLE = 'categories';

    /**
     * @return list<array<string, mixed>>
     */
    public function latest(int $limit = 5): array
    {
        $sql = strtr(
            <<<'SQL'
                SELECT *
                FROM {{table}}
                ORDER BY published_at DESC
                LIMIT {{limit}}
                SQL,
            [
                '{{table}}' => static::TABLE,
                '{{limit}}' => (string) $limit,
            ],
        );

        return $this->db->fetchAll($sql);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function byCategory(
        int $categoryId,
        ArticleSort $sort = ArticleSort::Date,
        SortDirection $direction = SortDirection::Desc,
        int $limit = 10,
        int $offset = 0,
    ): array {
        $sql = strtr(
            <<<'SQL'
                SELECT a.*
                FROM {{table}} AS a
                INNER JOIN {{pivot}} AS ac ON ac.article_id = a.id
                WHERE ac.category_id = {{category_id}}
                ORDER BY a.{{sort_column}} {{sort_direction}}
                LIMIT {{limit}} OFFSET {{offset}}
                SQL,
            [
                '{{table}}' => static::TABLE,
                '{{pivot}}' => self::CATEGORY_PIVOT_TABLE,
                '{{sort_column}}' => $sort->column(),
                '{{sort_direction}}' => $direction->value,
                '{{limit}}' => (string) $limit,
                '{{offset}}' => (string) $offset,
                '{{category_id}}' => (string) $categoryId,
            ],
        );

        return $this->db->fetchAll($sql);
    }

    public function countByCategory(int $categoryId): int
    {
        $sql = strtr(
            <<<'SQL'
                SELECT COUNT(*) AS total
                FROM {{pivot}}
                WHERE category_id = {{category_id}}
                SQL,
            [
                '{{pivot}}' => self::CATEGORY_PIVOT_TABLE,
                '{{category_id}}' => $categoryId
            ],
        );

        $row = $this->db->fetchOne($sql);

        return (int) ($row['total'] ?? 0);
    }

    /**
     * Вариант 1: window function (ROW_NUMBER).
     *
     * Один запрос. БД нумерует статьи внутри каждой категории по дате и
     * оставляет первые N. Движок читает и сортирует все строки категории и
     * только потом отбрасывает лишние.
     *
     * @return array<int, list<array<string, mixed>>>
     */
    public function latestPerCategoryWindow(int $perCategory = 3): array
    {
        $sql = strtr(
            <<<'SQL'
                SELECT ranked.id, ranked.title, ranked.description, ranked.`text`,
                       ranked.image, ranked.views, ranked.published_at, ranked.category_id
                FROM (
                    SELECT
                        a.*,
                        ac.category_id,
                        ROW_NUMBER() OVER (
                            PARTITION BY ac.category_id
                            ORDER BY a.published_at DESC
                        ) AS rn
                    FROM {{table}} AS a
                    INNER JOIN {{pivot}} AS ac ON ac.article_id = a.id
                ) AS ranked
                WHERE ranked.rn <= {{per_category}}
                ORDER BY ranked.category_id, ranked.published_at DESC
                SQL,
            [
                '{{table}}' => static::TABLE,
                '{{pivot}}' => self::CATEGORY_PIVOT_TABLE,
                '{{per_category}}' => (string) $perCategory,
            ],
        );

        $rows = $this->db->fetchAll($sql);

        return $this->groupByCategory($rows);
    }

    /**
     * Вариант 2: LATERAL join (MySQL 8.0.14+).
     *
     * Один запрос. Для каждой категории выполняется ограниченный LIMIT-подзапрос,
     * который с индексом (category_id, published_at) останавливается на N-й строке
     * и не читает остальные — масштабируется независимо от размера категории.
     *
     * @return array<int, list<array<string, mixed>>>
     */
    public function latestPerCategoryLateral(int $perCategory = 3): array
    {
        $sql = strtr(
            <<<'SQL'
                SELECT top.*
                FROM {{categories}} AS c
                JOIN LATERAL (
                    SELECT a.*, ac.category_id
                    FROM {{table}} AS a
                    INNER JOIN {{pivot}} AS ac ON ac.article_id = a.id
                    WHERE ac.category_id = c.id
                    ORDER BY a.published_at DESC
                    LIMIT {{per_category}}
                ) AS top ON TRUE
                ORDER BY top.category_id, top.published_at DESC
                SQL,
            [
                '{{table}}' => static::TABLE,
                '{{pivot}}' => self::CATEGORY_PIVOT_TABLE,
                '{{categories}}' => self::CATEGORIES_TABLE,
                '{{per_category}}' => (string) $perCategory,
            ],
        );

        $rows = $this->db->fetchAll($sql);

        return $this->groupByCategory($rows);
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupByCategory(array $rows): array
    {
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[(int) $row['category_id']][] = $row;
        }

        return $grouped;
    }
}
