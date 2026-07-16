<?php

namespace App\Core;

interface TemplateEngine
{
    public function setup(string $templatesDir, string $cacheDir): void;

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string;
}
