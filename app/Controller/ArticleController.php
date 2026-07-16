<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\ArticleRepository;

class ArticleController extends AbstractController
{
    public function show(string $id): string
    {
        $article = $this->container->get(ArticleRepository::class)->find((int) $id);

        if ($article === null) {
            http_response_code(404);

            return 'Article not found';
        }

        return $this->container->get(View::class)->render('article.tpl', [
            'article' => $article,
        ]);
    }
}
