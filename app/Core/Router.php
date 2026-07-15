<?php

declare(strict_types=1);

namespace App\Core;

use App\Controller\ArticleController;
use App\Controller\ErrorPageController;
use App\Controller\FrontPageController;

class Router
{
    public function dispatch(): void
    {
        $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        [$action, $detail] = $segments = explode('/', $path);

        if (count($segments) !== 2) {
            (new ErrorPageController())->error();
            return;
        }

        [$controller, $method] = match ($action) {
            '' => [new FrontPageController(), 'index'],
            'article' => [new ArticleController(), 'show'],
            default => [new ErrorPageController(), 'error'],
        };

        $controller->{$method}($detail);
    }
}
