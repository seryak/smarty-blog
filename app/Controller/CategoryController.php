<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Paginator;
use App\Core\Request;
use App\Core\View;
use App\Enum\ArticleSort;
use App\Enum\SortDirection;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

class CategoryController extends AbstractController
{
    private const PER_PAGE = 1;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ArticleRepository $articleRepository,
        private readonly Request $request,
        View $view,
    ) {
        parent::__construct($view);
    }

    public function show(string $id): string
    {
        $categoryId = (int) $id;
        $category = $this->categoryRepository->find($categoryId);

        if ($category === null) {
            return $this->notFound('Category not found');
        }

        $sort = ArticleSort::tryFrom($this->request->query('sort', ArticleSort::Date->value));
        $direction = SortDirection::tryFrom($this->request->query('dir', SortDirection::Desc->value));

        $paginator = new Paginator(
            (int) $this->request->query('page', '1'),
            self::PER_PAGE,
            $this->articleRepository->countByCategory($categoryId)
        );
        $articles = $this->articleRepository->byCategory($categoryId, $sort, $direction, $paginator->limit(), $paginator->offset());

        return $this->view->render('category.tpl', [
            'category' => $category,
            'articles' => $articles,
            'paginator' => $paginator,
            'sort' => $sort->value,
            'direction' => $direction->value,
        ]);
    }
}
