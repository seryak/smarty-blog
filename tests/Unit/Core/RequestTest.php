<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core;

use App\Core\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Request::class)]
final class RequestTest extends TestCase
{
    #[TestDox('query() возвращает значение параметра')]
    public function test_query_returns_parameter_value(): void
    {
        $request = new Request(['sort' => 'views']);

        self::assertSame('views', $request->query('sort'));
    }

    #[TestDox('query() возвращает null для отсутствующего параметра без дефолта')]
    public function test_query_returns_null_for_missing_parameter(): void
    {
        $request = new Request();

        self::assertNull($request->query('sort'));
    }

    #[TestDox('query() возвращает дефолт для отсутствующего параметра')]
    public function test_query_returns_default_for_missing_parameter(): void
    {
        $request = new Request();

        self::assertSame('date', $request->query('sort', 'date'));
    }

    #[TestDox('query() приводит скалярное значение к строке')]
    public function test_query_casts_scalar_value_to_string(): void
    {
        $request = new Request(['page' => 2]);

        self::assertSame('2', $request->query('page'));
    }

    #[TestDox('query() возвращает дефолт для не-скалярного значения')]
    public function test_query_returns_default_for_non_scalar_value(): void
    {
        $request = new Request(['ids' => ['1', '2']]);

        self::assertSame('fallback', $request->query('ids', 'fallback'));
    }

    #[TestDox('uri() возвращает REQUEST_URI')]
    public function test_uri_returns_request_uri(): void
    {
        $request = new Request(server: ['REQUEST_URI' => '/category/7?page=2']);

        self::assertSame('/category/7?page=2', $request->uri());
    }

    #[TestDox('uri() возвращает / при отсутствии REQUEST_URI')]
    public function test_uri_defaults_to_slash_when_missing(): void
    {
        $request = new Request();

        self::assertSame('/', $request->uri());
    }

    #[TestDox('uri() возвращает / для не-строкового REQUEST_URI')]
    public function test_uri_defaults_to_slash_for_non_string(): void
    {
        $request = new Request(server: ['REQUEST_URI' => ['/weird']]);

        self::assertSame('/', $request->uri());
    }
}
