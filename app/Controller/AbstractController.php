<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;

abstract class AbstractController
{
    public function __construct(protected readonly View $view)
    {
    }

    protected function notFound(string $message = 'Not found'): string
    {
        http_response_code(404);

        return $this->view->render('404.tpl', [
            'message' => $message,
        ]);
    }
}
