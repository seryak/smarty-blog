<?php

declare(strict_types=1);

namespace App\Core;

class App
{
    public function __construct(
        protected readonly Container $container,
        protected readonly ControllerAction $action,
    ) {
    }

    public function run(): void
    {
        $this->action->setContainer($this->container);
        echo ($this->action)();
    }
}
