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
        $segments = $path === '' ? [] : explode('/', $path);

        match (true) {
            $segments === [] => (new FrontPageController())->index(),
            count($segments) === 2 && $segments[0] === 'article' => (new ArticleController())->show($segments[1]),
            default => (new ErrorPageController())->error(),
        };
    }

}
