<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Container;

abstract class AbstractController
{
    protected Container $container;
    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }
}
