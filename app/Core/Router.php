<?php

declare(strict_types=1);

namespace App\Core;

use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\Controller\ErrorPageController;
use App\Controller\FrontPageController;
use App\DTO\RouteDTO;

class Router
{
    private readonly UrlParser $urlParser;
    private readonly Request $request;

    public function __construct(
        Request $request,
    ) {
        $this->urlParser = new UrlParser();
        $this->request = $request;
    }

    public function resolve(): RouteDTO
    {
        $segments = $this->urlParser->parse($this->request->uri());

        return match (true) {
            $segments === [] => new RouteDTO(FrontPageController::class, 'index'),
            count($segments) === 2 && $segments[0] === 'article' => new RouteDTO(ArticleController::class, 'show', [$segments[1]]),
            count($segments) === 2 && $segments[0] === 'category' => new RouteDTO(CategoryController::class, 'show', [$segments[1]]),
            default => new RouteDTO(ErrorPageController::class, 'error'),
        };
    }
}
