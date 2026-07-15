<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core;

use App\Core\UrlParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(UrlParser::class)]
final class UrlParserTest extends TestCase
{
    #[TestDox('Корневой путь возвращает пустой массив сегментов')]
    public function test_parse_root_path_returns_empty_array(): void
    {
        self::assertSame([], (new UrlParser())->parse('/'));
    }

    #[TestDox('Путь разбивается на сегменты по /')]
    public function test_parse_splits_path_into_segments(): void
    {
        self::assertSame(['article', '42'], (new UrlParser())->parse('/article/42'));
    }

    #[TestDox('Query-строка игнорируется при разборе пути')]
    public function test_parse_ignores_query_string(): void
    {
        self::assertSame(['article', '42'], (new UrlParser())->parse('/article/42?ref=newsletter'));
    }

    #[TestDox('Конечный слеш обрезается при разборе')]
    public function test_parse_trims_trailing_slash(): void
    {
        self::assertSame(['article', '42'], (new UrlParser())->parse('/article/42/'));
    }
}
