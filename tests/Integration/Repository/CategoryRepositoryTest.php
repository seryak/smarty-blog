<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Repository\CategoryRepository;
use App\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(CategoryRepository::class)]
final class CategoryRepositoryTest extends IntegrationTestCase
{
    #[TestDox('hasArticles() возвращает только категории, у которых есть статьи')]
    public function test_has_articles_returns_only_categories_with_articles(): void
    {
        $withArticles = $this->insertCategory('С статьёй');
        $empty = $this->insertCategory('Пустая');

        $articleId = $this->insertArticle();
        $this->linkArticleCategory($articleId, $withArticles);

        $repository = new CategoryRepository($this->db);
        $ids = array_map(static fn (array $row): int => (int) $row['id'], $repository->hasArticles());

        self::assertContains($withArticles, $ids);
        self::assertNotContains($empty, $ids);
    }

    #[TestDox('hasArticles() возвращает категорию один раз, даже если у неё несколько статей')]
    public function test_has_articles_returns_category_once(): void
    {
        $category = $this->insertCategory('С двумя статьями');

        foreach (['A', 'B'] as $title) {
            $articleId = $this->insertArticle(['title' => $title]);
            $this->linkArticleCategory($articleId, $category);
        }

        $repository = new CategoryRepository($this->db);
        $ids = array_map(static fn (array $row): int => (int) $row['id'], $repository->hasArticles());

        self::assertSame([$category], array_values(array_filter($ids, static fn (int $id): bool => $id === $category)));
    }
}
