<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core;

use App\Controller\AbstractController;
use App\Core\App;
use App\Core\Container;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

#[CoversClass(App::class)]
final class AppTest extends TestCase
{
    #[TestDox('run() передаёт Container в controller экшена перед его вызовом')]
    public function test_run_injects_container_into_action_controller(): void
    {
        $container = new Container();
        $app = new App($container, '/');
        $app->run();

        $action = (new ReflectionProperty(App::class, 'action'))->getValue($app);
        $controllerContainer = (new ReflectionProperty(AbstractController::class, 'container'))->getValue($action->controller);

        self::assertSame($container, $controllerContainer);
    }
}
