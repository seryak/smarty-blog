<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Container
{
    /** @var array<string, \Closure> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $instances = [];

    public function bind(string $id, \Closure $factory): void
    {
        $this->bindings[$id] = $factory;
    }

    public function get(string $id): object
    {
        if (!isset($this->instances[$id])) {
            if (!isset($this->bindings[$id])) {
                throw new RuntimeException("No binding for {$id}");
            }

            $this->instances[$id] = ($this->bindings[$id])($this);
        }

        return $this->instances[$id];
    }
}
