<?php

namespace App\Core;

use Smarty\Smarty;

class SmartyEngine implements TemplateEngine
{
    private Smarty $smarty;

    public function __construct(Smarty $smarty = new Smarty())
    {
        $this->smarty = $smarty;
    }

    public function setup(string $templatesDir, string $cacheDir): void
    {
        $this->smarty->setTemplateDir($templatesDir);
        $this->smarty->setCompileDir($cacheDir . '/compile');
        $this->smarty->setCacheDir($cacheDir . '/cache');
        $this->smarty->setEscapeHtml(true);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $this->smarty->assign($data);
        return $this->smarty->fetch($template);
    }
}
