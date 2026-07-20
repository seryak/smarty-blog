<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core;

use App\Controller\AbstractController;
use App\Core\App;
use App\Core\ControllerAction;
use App\Core\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(App::class)]
final class AppTest extends TestCase
{
    #[TestDox('run() вызывает подготовленный ControllerAction и выводит результат')]
    public function test_run_invokes_prepared_action_and_outputs_result(): void
    {
        $controller = new class ($this->createStub(View::class)) extends AbstractController {
            public function handle(): string
            {
                return 'test';
            }
        };

        $action = new ControllerAction($controller, 'handle');
        $app = new App($action, $this->createStub(View::class));

        ob_start();
        $app->run();
        $output = ob_get_clean();

        self::assertSame('test', $output);
    }

    #[TestDox('run() при исключении в экшене рендерит 500.tpl со статусом 500')]
    public function test_run_renders_500_page_on_throwable(): void
    {
        $controller = new class ($this->createStub(View::class)) extends AbstractController {
            public function handle(): string
            {
                throw new RuntimeException('boom');
            }
        };

        $view = $this->createMock(View::class);
        $view->expects($this->once())
            ->method('render')
            ->with('500.tpl')
            ->willReturn('server error page');

        $action = new ControllerAction($controller, 'handle');
        $app = new App($action, $view);

        ob_start();
        $app->run();
        $output = ob_get_clean();

        self::assertSame('server error page', $output);
        self::assertSame(500, http_response_code());
    }
}
