<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core;

use App\Core\Container;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

#[CoversClass(Container::class)]
final class ContainerTest extends TestCase
{
    #[TestDox('get() возвращает объект, созданный фабрикой из bind()')]
    public function test_get_returns_instance_built_by_factory(): void
    {
        $container = new Container();
        $container->bind(stdClass::class, function () {
            $instance = new stdClass();
            $instance->name = 'built';

            return $instance;
        });

        $result = $container->get(stdClass::class);

        self::assertSame('built', $result->name);
    }

    #[TestDox('get() кэширует инстанс и вызывает фабрику только один раз')]
    public function test_get_caches_instance_and_calls_factory_once(): void
    {
        $callCount = 0;
        $container = new Container();
        $container->bind(stdClass::class, function () use (&$callCount) {
            $callCount++;

            return new stdClass();
        });

        $first = $container->get(stdClass::class);
        $second = $container->get(stdClass::class);

        self::assertSame($first, $second);
        self::assertSame(1, $callCount);
    }

    #[TestDox('get() без предварительного bind() бросает RuntimeException')]
    public function test_get_throws_when_no_binding_exists(): void
    {
        $container = new Container();

        $this->expectException(RuntimeException::class);

        $container->get(stdClass::class);
    }

    #[TestDox('Фабрика получает сам контейнер первым аргументом')]
    public function test_factory_receives_container_as_first_argument(): void
    {
        $container = new Container();
        $container->bind(stdClass::class, function (Container $c) {
            $instance = new stdClass();
            $instance->receivedSameContainer = $c;

            return $instance;
        });

        $result = $container->get(stdClass::class);

        self::assertSame($container, $result->receivedSameContainer);
    }
}
