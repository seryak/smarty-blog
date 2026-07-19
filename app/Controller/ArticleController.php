<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\ArticleRepository;

class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly View $view,
    ) {
    }

    public function show(string $id): string
    {
        $article = $this->articleRepository->find((int) $id);

        if ($article === null) {
            http_response_code(404);

            return 'Article not found';
        }

        return $this->view->render('article.tpl', [
            'article' => $article,
            'similar' => $this->articleRepository->similar((int) $id),
        ]);
    }
}
