<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

class App
{
    public function __construct(
        protected readonly ControllerAction $action,
        protected readonly View $view,
    ) {
    }

    public function run(): void
    {
        try {
            echo ($this->action)();
        } catch (Throwable $e) {
            $this->renderServerError($e);
        }
    }

    private function renderServerError(Throwable $e): void
    {
        error_log((string) $e);
        http_response_code(500);

        try {
            echo $this->view->render('500.tpl');
        } catch (Throwable) {
            // На случай, если сам рендер шаблона недоступен
            echo 'Internal Server Error';
        }
    }
}
