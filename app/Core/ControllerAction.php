<?php

declare(strict_types=1);

namespace App\Core;

final class ControllerAction
{
    /**
     * @param list<mixed> $arguments
     */
    public function __construct(
        public readonly object $controller,
        public readonly string $method,
        private readonly array $arguments = [],
    ) {
    }

    public function __invoke(): string
    {
        return $this->controller->{$this->method}(...$this->arguments);
    }
}
