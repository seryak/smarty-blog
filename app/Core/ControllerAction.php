<?php

declare(strict_types=1);

namespace App\Core;

use App\Controller\AbstractController;

final class ControllerAction
{
    /**
     * @param list<mixed> $arguments
     */
    public function __construct(
        public readonly AbstractController $controller,
        public readonly string $method,
        private readonly array $arguments = [],
    ) {
    }

    public function setContainer(Container $container): void
    {
        $this->controller->setContainer($container);
    }

    public function __invoke(): string
    {
        return $this->controller->{$this->method}(...$this->arguments);
    }
}
