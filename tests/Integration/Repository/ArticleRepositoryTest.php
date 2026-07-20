<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Enum\ArticleSort;
use App\Enum\SortDirection;
use App\Repository\ArticleRepository;
use App\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(ArticleRepository::class)]
final class ArticleRepositoryTest extends IntegrationTestCase
{
    #[TestDox('find() возвращает статью по id')]
    public function test_find_returns_row_by_id(): void
    {
        $id = $this->insertArticle(['title' => 'Найди меня', 'views' => 42]);

        $repository = new ArticleRepository($this->db);
        $row = $repository->find($id);

        self::assertNotNull($row);
        self::assertSame('Найди меня', $row['title']);
        self::assertSame(42, (int) $row['views']);
    }

    #[TestDox('find() возвращает null для несуществующего id')]
    public function test_find_returns_null_when_missing(): void
    {
        $repository = new ArticleRepository($this->db);

        self::assertNull($repository->find(999999));
    }

    #[TestDox('latest() возвращает статьи по убыванию published_at с учётом лимита')]
    public function test_latest_returns_articles_ordered_by_date_desc_with_limit(): void
    {
        $this->insertArticle(['title' => 'Старая', 'published_at' => '2026-01-01 00:00:00']);
        $this->insertArticle(['title' => 'Свежая', 'published_at' => '2026-03-01 00:00:00']);
        $this->insertArticle(['title' => 'Средняя', 'published_at' => '2026-02-01 00:00:00']);

        $repository = new ArticleRepository($this->db);
        $rows = $repository->latest(2);

        self::assertCount(2, $rows);
        self::assertSame('Свежая', $rows[0]['title']);
        self::assertSame('Средняя', $rows[1]['title']);
    }

    #[TestDox('byCategory() возвращает все статьи категории по убыванию published_at')]
    public function test_by_category_returns_all_category_articles_ordered_by_date_desc(): void
    {
        $category = $this->insertCategory('Категория');
        foreach (['2026-01-01', '2026-03-01', '2026-02-01'] as $i => $date) {
            $articleId = $this->insertArticle(['title' => "Статья {$i}", 'published_at' => "{$date} 00:00:00"]);
            $this->linkArticleCategory($articleId, $category);
        }

        $repository = new ArticleRepository($this->db);
        $rows = $repository->byCategory($category);

        self::assertCount(3, $rows);
        self::assertSame(
            ['2026-03-01 00:00:00', '2026-02-01 00:00:00', '2026-01-01 00:00:00'],
            array_column($rows, 'published_at'),
        );
    }

    #[TestDox('byCategory() не возвращает статьи чужих категорий')]
    public function test_by_category_excludes_articles_of_other_categories(): void
    {
        $target = $this->insertCategory('Целевая');
        $other = $this->insertCategory('Чужая');

        $targetArticle = $this->insertArticle(['title' => 'Наша']);
        $this->linkArticleCategory($targetArticle, $target);

        $otherArticle = $this->insertArticle(['title' => 'Чужая статья']);
        $this->linkArticleCategory($otherArticle, $other);

        $repository = new ArticleRepository($this->db);
        $titles = array_column($repository->byCategory($target), 'title');

        self::assertSame(['Наша'], $titles);
    }

    #[TestDox('byCategory() возвращает пустой массив для категории без статей')]
    public function test_by_category_returns_empty_array_when_no_articles(): void
    {
        $category = $this->insertCategory('Пустая');

        $repository = new ArticleRepository($this->db);

        self::assertSame([], $repository->byCategory($category));
    }

    #[TestDox('byCategory() сортирует по просмотрам по убыванию')]
    public function test_by_category_sorts_by_views_desc(): void
    {
        $category = $this->insertCategory('Категория');
        foreach (['Мало' => 5, 'Много' => 100, 'Средне' => 50] as $title => $views) {
            $articleId = $this->insertArticle(['title' => $title, 'views' => $views]);
            $this->linkArticleCategory($articleId, $category);
        }

        $repository = new ArticleRepository($this->db);
        $titles = array_column(
            $repository->byCategory($category, ArticleSort::Views, SortDirection::Desc),
            'title',
        );

        self::assertSame(['Много', 'Средне', 'Мало'], $titles);
    }

    #[TestDox('byCategory() сортирует по дате по возрастанию')]
    public function test_by_category_sorts_by_date_ascending(): void
    {
        $category = $this->insertCategory('Категория');
        foreach (['2026-03-01', '2026-01-01', '2026-02-01'] as $date) {
            $articleId = $this->insertArticle(['title' => $date, 'published_at' => "{$date} 00:00:00"]);
            $this->linkArticleCategory($articleId, $category);
        }

        $repository = new ArticleRepository($this->db);
        $titles = array_column(
            $repository->byCategory($category, ArticleSort::Date, SortDirection::Asc),
            'title',
        );

        self::assertSame(['2026-01-01', '2026-02-01', '2026-03-01'], $titles);
    }

    #[TestDox('byCategory() применяет limit и offset (пагинация)')]
    public function test_by_category_applies_limit_and_offset(): void
    {
        $category = $this->insertCategory('Категория');
        foreach (range(1, 5) as $i) {
            $articleId = $this->insertArticle([
                'title' => "Статья {$i}",
                'published_at' => sprintf('2026-01-%02d 00:00:00', $i),
            ]);
            $this->linkArticleCategory($articleId, $category);
        }

        $repository = new ArticleRepository($this->db);
        // По убыванию даты: 5,4,3,2,1. Страница 2 при 2 на страницу (offset=2, limit=2) -> 3-я и 2-я.
        $rows = $repository->byCategory($category, ArticleSort::Date, SortDirection::Desc, 2, 2);

        self::assertSame(['Статья 3', 'Статья 2'], array_column($rows, 'title'));
    }

    #[TestDox('countByCategory() возвращает число статей в категории')]
    public function test_count_by_category_returns_number_of_linked_articles(): void
    {
        $category = $this->insertCategory('Целевая');
        $other = $this->insertCategory('Другая');

        foreach (range(1, 3) as $i) {
            $articleId = $this->insertArticle(['title' => "A{$i}"]);
            $this->linkArticleCategory($articleId, $category);
        }
        $otherArticle = $this->insertArticle(['title' => 'B']);
        $this->linkArticleCategory($otherArticle, $other);

        $repository = new ArticleRepository($this->db);

        self::assertSame(3, $repository->countByCategory($category));
    }

    #[TestDox('latestPerCategoryWindow() возвращает топ-3 свежих статей на категорию, без пустых категорий')]
    public function test_latest_per_category_window_returns_top_n_ordered_by_date(): void
    {
        $category = $this->insertCategory('С пятью статьями');
        $empty = $this->insertCategory('Пустая');
        foreach (['2026-01-01', '2026-02-01', '2026-03-01', '2026-04-01', '2026-05-01'] as $i => $date) {
            $articleId = $this->insertArticle(['title' => "Статья {$i}", 'published_at' => "{$date} 00:00:00"]);
            $this->linkArticleCategory($articleId, $category);
        }

        $repository = new ArticleRepository($this->db);
        $grouped = $repository->latestPerCategoryWindow(3);

        self::assertArrayNotHasKey($empty, $grouped);
        self::assertCount(3, $grouped[$category]);
        self::assertSame(
            ['2026-05-01 00:00:00', '2026-04-01 00:00:00', '2026-03-01 00:00:00'],
            array_column($grouped[$category], 'published_at'),
        );
    }

    #[TestDox('latestPerCategoryLateral() возвращает топ-3 свежих статей на категорию, без пустых категорий')]
    public function test_latest_per_category_lateral_returns_top_n_ordered_by_date(): void
    {
        $category = $this->insertCategory('С пятью статьями');
        $empty = $this->insertCategory('Пустая');
        foreach (['2026-01-01', '2026-02-01', '2026-03-01', '2026-04-01', '2026-05-01'] as $i => $date) {
            $articleId = $this->insertArticle(['title' => "Статья {$i}", 'published_at' => "{$date} 00:00:00"]);
            $this->linkArticleCategory($articleId, $category);
        }

        $repository = new ArticleRepository($this->db);
        $grouped = $repository->latestPerCategoryLateral(3);

        self::assertArrayNotHasKey($empty, $grouped);
        self::assertCount(3, $grouped[$category]);
        self::assertSame(
            ['2026-05-01 00:00:00', '2026-04-01 00:00:00', '2026-03-01 00:00:00'],
            array_column($grouped[$category], 'published_at'),
        );
    }

    #[TestDox('Оба метода top-N дают одинаковый результат')]
    public function test_window_and_lateral_return_identical_result(): void
    {
        $categoryA = $this->insertCategory('A');
        $categoryB = $this->insertCategory('B');

        foreach (['2026-01-01', '2026-02-01', '2026-03-01', '2026-04-01'] as $i => $date) {
            $articleId = $this->insertArticle(['title' => "A{$i}", 'published_at' => "{$date} 00:00:00"]);
            $this->linkArticleCategory($articleId, $categoryA);
        }
        foreach (['2026-01-15', '2026-02-15'] as $i => $date) {
            $articleId = $this->insertArticle(['title' => "B{$i}", 'published_at' => "{$date} 00:00:00"]);
            $this->linkArticleCategory($articleId, $categoryB);
        }

        $repository = new ArticleRepository($this->db);

        self::assertEquals(
            $repository->latestPerCategoryWindow(3),
            $repository->latestPerCategoryLateral(3),
        );
    }
}
