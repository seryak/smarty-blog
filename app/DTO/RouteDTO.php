<?php

declare(strict_types=1);

namespace App\DTO;

use App\Controller\AbstractController;

final class RouteDTO
{
    /**
     * @param class-string<AbstractController> $controllerClass
     * @param list<mixed> $arguments
     */
    public function __construct(
        public readonly string $controllerClass,
        public readonly string $method,
        private readonly array $arguments = [],
    ) {
    }

    /**
     * @return list<mixed>
     */
    public function arguments(): array
    {
        return $this->arguments;
    }
}
