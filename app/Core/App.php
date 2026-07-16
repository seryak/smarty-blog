<?php

namespace App\Core;

class App
{

    protected ControllerAction $action;
    protected Container $container;

    public function __construct(Container $container, string $requestUri)
    {
        $this->container = $container;
        $router = new Router();
        $this->action = $router->resolve($requestUri);
    }

    public function run(): void
    {
        echo ($this->action)();
    }
}