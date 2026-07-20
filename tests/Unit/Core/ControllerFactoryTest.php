<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core;

use App\Controller\AbstractController;
use App\Core\Container;
use App\Core\ControllerAction;
use App\Core\ControllerFactory;
use App\Core\View;
use App\DTO\RouteDTO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

#[CoversClass(ControllerFactory::class)]
#[CoversClass(RouteDTO::class)]
#[CoversClass(ControllerAction::class)]
final class ControllerFactoryTest extends TestCase
{
    #[TestDox('create() собирает ControllerAction из route descriptor и контейнера')]
    public function test_create_returns_controller_action_from_route_descriptor_and_container(): void
    {
        $controller = new class ($this->createStub(View::class)) extends AbstractController {
            public function handle(string $value): string
            {
                return 'handled ' . $value;
            }
        };

        $container = new Container();
        $container->bind($controller::class, static fn () => $controller);

        $route = new RouteDTO($controller::class, 'handle', ['value']);
        $action = (new ControllerFactory($container))->create($route);

        self::assertSame($controller, $action->controller);
        self::assertSame('handle', $action->method);
        self::assertSame('handled value', $action());
    }

    #[TestDox('create() бросает RuntimeException если контейнер вернул не контроллер')]
    public function test_create_throws_when_resolved_service_is_not_controller(): void
    {
        $controller = new class ($this->createStub(View::class)) extends AbstractController {
        };

        $container = new Container();
        $container->bind($controller::class, static fn () => new stdClass());

        $factory = new ControllerFactory($container);

        $this->expectException(RuntimeException::class);

        $factory->create(new RouteDTO($controller::class, 'handle'));
    }
}
