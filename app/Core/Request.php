<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    /**
     * @param array<string, mixed> $query $_GET
     * @param array<string, mixed> $server $_SERVER
     */
    public function __construct(
        private readonly array $query = [],
        private readonly array $server = [],
    ) {
    }

    public function query(string $key, ?string $default = null): ?string
    {
        $value = $this->query[$key] ?? null;
        return is_scalar($value) ? (string) $value : $default;
    }

    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        return is_string($uri) ? $uri : '/';
    }
}
