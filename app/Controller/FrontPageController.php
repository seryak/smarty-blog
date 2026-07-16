<?php

declare(strict_types=1);

namespace App\Controller;

class FrontPageController extends AbstractController
{
    public function index(): string
    {
        return 'frontpage';
    }
}
