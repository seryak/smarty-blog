<?php

declare(strict_types=1);

namespace App\Controller;

class ArticleController extends AbstractController
{
    public function show(string $id): string
    {
        return 'articlepage ' . $id;
    }
}
