<?php

declare(strict_types=1);

namespace App\Controller;

class ArticleController
{
    public function show(string $id): string
    {
        return 'articlepage ' . $id;
    }
}
