<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

class FrontPageController extends AbstractController
{
    public function index(): string
    {
        $categories = $this->container->get(CategoryRepository::class)->hasArticles();
//        $articlesByCategory = $this->container->get(ArticleRepository::class)->latestPerCategoryWindow(3);
        $articlesByCategory = $this->container->get(ArticleRepository::class)->latestPerCategoryLateral(3);

        return $this->container->get(View::class)->render('front.tpl', [
            'categories' => $categories,
            'articlesByCategory' => $articlesByCategory,
        ]);
    }
}
