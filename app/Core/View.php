<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    private TemplateEngine $engine;

    public function __construct(TemplateEngine $engine, string $templatesDir, string $cacheDir)
    {
        $engine->setup($templatesDir, $cacheDir);
        $this->engine = $engine;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        return $this->engine->render($template, $data);
    }
}
