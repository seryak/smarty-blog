<?php

declare(strict_types=1);

namespace App\Core;

final class Paginator
{
    public function __construct(
        public readonly int $page,
        public readonly int $perPage,
        public readonly int $total,
    ) {}

    public function limit(): int
    {
        return $this->perPage;
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function totalPages(): int
    {
        return $this->perPage > 0 ? (int) ceil($this->total / $this->perPage) : 0;
    }

    public function hasPrev(): bool
    {
        return $this->page > 1;
    }

    public function hasNext(): bool
    {
        return $this->page < $this->totalPages();
    }
}
