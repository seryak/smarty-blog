<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core;

use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\Controller\ErrorPageController;
use App\Controller\FrontPageController;
use App\Core\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Router::class)]
final class RouterTest extends TestCase
{
    #[TestDox('Диспетчеризация корневого пути вызывает FrontPageController::index()')]
    public function test_resolve_root_path_to_front_page_index(): void
    {
        $action = (new Router())->resolve('/');

        self::assertInstanceOf(FrontPageController::class, $action->controller);
        self::assertSame('index', $action->method);
    }

    #[TestDox('Диспетчеризация /article/{id} вызывает ArticleController::show() с этим id')]
    public function test_resolve_article_path_to_article_show_with_id(): void
    {
        $action = (new Router())->resolve('/article/42');

        self::assertInstanceOf(ArticleController::class, $action->controller);
        self::assertSame('show', $action->method);
    }

    #[TestDox('Query-строка не влияет на диспетчеризацию /article/{id}')]
    public function test_resolve_article_path_ignores_query_string(): void
    {
        $action = (new Router())->resolve('/article/42?ref=newsletter');

        self::assertInstanceOf(ArticleController::class, $action->controller);
        self::assertSame('show', $action->method);
    }

    #[TestDox('Диспетчеризация /category/{id} вызывает CategoryController::show() с этим id')]
    public function test_resolve_category_path_to_category_show_with_id(): void
    {
        $action = (new Router())->resolve('/category/7');

        self::assertInstanceOf(CategoryController::class, $action->controller);
        self::assertSame('show', $action->method);
    }

    #[TestDox('Без id статьи диспетчеризация уходит на ErrorPageController::error()')]
    public function test_resolve_article_without_id_to_error_page(): void
    {
        $action = (new Router())->resolve('/article');

        self::assertInstanceOf(ErrorPageController::class, $action->controller);
        self::assertSame('error', $action->method);
    }

    #[TestDox('Лишний сегмент после id статьи приводит к странице ошибки')]
    public function test_resolve_article_with_extra_segment_to_error_page(): void
    {
        $action = (new Router())->resolve('/article/42/comments');

        self::assertInstanceOf(ErrorPageController::class, $action->controller);
        self::assertSame('error', $action->method);
    }

    #[TestDox('Неизвестный путь из одного сегмента приводит к странице ошибки')]
    public function test_resolve_unknown_single_segment_to_error_page(): void
    {
        $action = (new Router())->resolve('/unknown');

        self::assertInstanceOf(ErrorPageController::class, $action->controller);
        self::assertSame('error', $action->method);
    }

    #[TestDox('Неизвестное действие с двумя сегментами приводит к странице ошибки')]
    public function test_resolve_unknown_two_segment_action_to_error_page(): void
    {
        $action = (new Router())->resolve('/foo/bar');

        self::assertInstanceOf(ErrorPageController::class, $action->controller);
        self::assertSame('error', $action->method);
    }
}
