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
        $rows = $this->db->fetchAll(
            'SELECT ranked.*
             FROM (
                 SELECT
                     a.*,
                     ac.category_id,
                     ROW_NUMBER() OVER (
                         PARTITION BY ac.category_id
                         ORDER BY a.published_at DESC
                     ) AS rn
                 FROM articles a
                 JOIN article_category ac ON ac.article_id = a.id
             ) ranked
             WHERE ranked.rn <= :per_category
             ORDER BY ranked.category_id, ranked.published_at DESC',
            ['per_category' => $perCategory],
        );

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
        $rows = $this->db->fetchAll(
            'SELECT top.*
             FROM categories c
             JOIN LATERAL (
                 SELECT a.*, ac.category_id
                 FROM articles a
                 JOIN article_category ac ON ac.article_id = a.id
                 WHERE ac.category_id = c.id
                 ORDER BY a.published_at DESC
                 LIMIT :per_category
             ) top ON TRUE
             ORDER BY top.category_id, top.published_at DESC',
            ['per_category' => $perCategory],
        );

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
