<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;

class ErrorPageController extends AbstractController
{
    public function __construct(View $view)
    {
        parent::__construct($view);
    }

    public function error(): string
    {
        return $this->notFound();
    }
}
