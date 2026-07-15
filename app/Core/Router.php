<?php

declare(strict_types=1);

namespace App\Core;

use App\Controller\ArticleController;
use App\Controller\ErrorPageController;
use App\Controller\FrontPageController;

class Router
{
    private readonly UrlParser $urlParser;

    public function __construct(
    ) {
        $this->urlParser = new UrlParser();
    }

    public function resolve(string $requestUri): ControllerAction
    {
        $segments = $this->urlParser->parse($requestUri);

        return match (true) {
            $segments === [] => new ControllerAction(new FrontPageController(), 'index'),
            count($segments) === 2 && $segments[0] === 'article' => new ControllerAction(new ArticleController(), 'show', [$segments[1]]),
            default => new ControllerAction(new ErrorPageController(), 'error'),
        };
    }
}
