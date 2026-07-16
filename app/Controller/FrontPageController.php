<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\ArticleRepository;

class FrontPageController extends AbstractController
{
    public function index(): string
    {
        $articles = $this->container->get(ArticleRepository::class)->latest();

        return $this->container->get(View::class)->render('front.tpl', ['articles' => $articles]);
    }
}
