<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

class CategoryController extends AbstractController
{
    public function show(string $id): string
    {
        $categoryId = (int) $id;
        $category = $this->container->get(CategoryRepository::class)->find($categoryId);

        if ($category === null) {
            http_response_code(404);

            return 'Category not found';
        }

        $articles = $this->container->get(ArticleRepository::class)->byCategory($categoryId);

        return $this->container->get(View::class)->render('category.tpl', [
            'category' => $category,
            'articles' => $articles,
        ]);
    }
}
