<?php

declare(strict_types=1);

namespace App\Core;

use App\Controller\AbstractController;
use App\DTO\RouteDTO;
use RuntimeException;

final class ControllerFactory
{
    public function __construct(private readonly Container $container)
    {
    }

    public function create(RouteDTO $route): ControllerAction
    {
        $controller = $this->container->get($route->controllerClass);

        if (!$controller instanceof AbstractController) {
            throw new RuntimeException(sprintf(
                'Controller "%s" must extend %s.',
                $route->controllerClass,
                AbstractController::class,
            ));
        }

        return new ControllerAction($controller, $route->method, $route->arguments());
    }
}
