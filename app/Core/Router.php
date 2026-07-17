<?php

declare(strict_types=1);

namespace App\Core;

use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\Controller\ErrorPageController;
use App\Controller\FrontPageController;

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

    public function resolve(): ControllerAction
    {
        $segments = $this->urlParser->parse($this->request->uri());

        return match (true) {
            $segments === [] => new ControllerAction(new FrontPageController(), 'index'),
            count($segments) === 2 && $segments[0] === 'article' => new ControllerAction(new ArticleController(), 'show', [$segments[1]]),
            count($segments) === 2 && $segments[0] === 'category' => new ControllerAction(new CategoryController(), 'show', [$segments[1]]),
            default => new ControllerAction(new ErrorPageController(), 'error'),
        };
    }
}
