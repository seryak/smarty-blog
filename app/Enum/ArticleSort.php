<?php

declare(strict_types=1);

namespace App\Enum;

/** Значение = реальная колонка в таблице articles. */
enum ArticleSort: string
{
    case Date = 'published_at';
    case Views = 'views';

    public function column(): string
    {
        return $this->value;
    }
}
