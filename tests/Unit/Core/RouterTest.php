<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core;

use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\Controller\ErrorPageController;
use App\Controller\FrontPageController;
use App\Core\Request;
use App\Core\Router;
use App\DTO\RouteDTO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Router::class)]
#[CoversClass(RouteDTO::class)]
final class RouterTest extends TestCase
{
    private function getRequest(string $uri): Request
    {
        return new Request(server: ['REQUEST_URI' => $uri]);
    }

    #[TestDox('Диспетчеризация корневого пути вызывает FrontPageController::index()')]
    public function test_resolve_root_path_to_front_page_index(): void
    {
        $route = (new Router($this->getRequest('/')))->resolve();

        self::assertSame(FrontPageController::class, $route->controllerClass);
        self::assertSame('index', $route->method);
        self::assertSame([], $route->arguments());
    }

    #[TestDox('Диспетчеризация /article/{id} вызывает ArticleController::show() с этим id')]
    public function test_resolve_article_path_to_article_show_with_id(): void
    {
        $route = (new Router($this->getRequest('/article/42')))->resolve();

        self::assertSame(ArticleController::class, $route->controllerClass);
        self::assertSame('show', $route->method);
        self::assertSame(['42'], $route->arguments());
    }

    #[TestDox('Query-строка не влияет на диспетчеризацию /article/{id}')]
    public function test_resolve_article_path_ignores_query_string(): void
    {
        $route = (new Router($this->getRequest('/article/42?ref=newsletter')))->resolve();

        self::assertSame(ArticleController::class, $route->controllerClass);
        self::assertSame('show', $route->method);
        self::assertSame(['42'], $route->arguments());
    }

    #[TestDox('Диспетчеризация /category/{id} вызывает CategoryController::show() с этим id')]
    public function test_resolve_category_path_to_category_show_with_id(): void
    {
        $route = (new Router($this->getRequest('/category/7')))->resolve();

        self::assertSame(CategoryController::class, $route->controllerClass);
        self::assertSame('show', $route->method);
        self::assertSame(['7'], $route->arguments());
    }

    #[TestDox('Без id статьи диспетчеризация уходит на ErrorPageController::error()')]
    public function test_resolve_article_without_id_to_error_page(): void
    {
        $route = (new Router($this->getRequest('/article')))->resolve();

        self::assertSame(ErrorPageController::class, $route->controllerClass);
        self::assertSame('error', $route->method);
        self::assertSame([], $route->arguments());
    }

    #[TestDox('Лишний сегмент после id статьи приводит к странице ошибки')]
    public function test_resolve_article_with_extra_segment_to_error_page(): void
    {
        $route = (new Router($this->getRequest('/article/42/comments')))->resolve();

        self::assertSame(ErrorPageController::class, $route->controllerClass);
        self::assertSame('error', $route->method);
        self::assertSame([], $route->arguments());
    }

    #[TestDox('Неизвестный путь из одного сегмента приводит к странице ошибки')]
    public function test_resolve_unknown_single_segment_to_error_page(): void
    {
        $route = (new Router($this->getRequest('/unknown')))->resolve();

        self::assertSame(ErrorPageController::class, $route->controllerClass);
        self::assertSame('error', $route->method);
        self::assertSame([], $route->arguments());
    }

    #[TestDox('Неизвестное действие с двумя сегментами приводит к странице ошибки')]
    public function test_resolve_unknown_two_segment_action_to_error_page(): void
    {
        $route = (new Router($this->getRequest('/foo/bar')))->resolve();

        self::assertSame(ErrorPageController::class, $route->controllerClass);
        self::assertSame('error', $route->method);
        self::assertSame([], $route->arguments());
    }
}
