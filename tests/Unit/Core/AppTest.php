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
        $app = new App($action);

        ob_start();
        $app->run();
        $output = ob_get_clean();

        self::assertSame('test', $output);
    }
}
