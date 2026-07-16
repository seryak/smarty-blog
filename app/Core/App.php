<?php

namespace App\Core;

class App
{

    protected ControllerAction $action;

    public function __construct(string $requestUri)
    {
        $router = new Router();
        $this->action = $router->resolve($requestUri);
    }

    public function run(): void
    {
        echo ($this->action)();
    }
}