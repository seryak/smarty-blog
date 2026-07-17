<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

class FrontPageController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ArticleRepository $articleRepository,
        private readonly View $view,
    ) {
    }

    public function index(): string
    {
        $categories = $this->categoryRepository->hasArticles();
        //        $articlesByCategory = $this->articleRepository->latestPerCategoryWindow(3);
        $articlesByCategory = $this->articleRepository->latestPerCategoryLateral(3);

        return $this->view->render('front.tpl', [
            'categories' => $categories,
            'articlesByCategory' => $articlesByCategory,
        ]);
    }
}
