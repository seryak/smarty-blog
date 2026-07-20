<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\ArticleRepository;

class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        View $view,
    ) {
        parent::__construct($view);
    }

    public function show(string $id): string
    {
        $article = $this->articleRepository->find((int) $id);

        if ($article === null) {
            return $this->notFound('Article not found');
        }

        return $this->view->render('article.tpl', [
            'article' => $article,
            'similar' => $this->articleRepository->similar((int) $id),
        ]);
    }
}
