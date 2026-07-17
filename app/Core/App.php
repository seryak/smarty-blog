<?php

declare(strict_types=1);

namespace App\Core;

class App
{
    public function __construct(
        protected readonly ControllerAction $action,
    ) {
    }

    public function run(): void
    {
        echo ($this->action)();
    }
}
