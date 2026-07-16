<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core;

use App\Core\SmartyEngine;
use App\Core\TemplateEngine;
use App\Core\View;
use FilesystemIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(View::class)]
final class ViewTest extends TestCase
{
    private TemplateEngine&MockObject $engine;

    protected function setUp(): void
    {
        $this->engine = $this->createMock(TemplateEngine::class);
    }

    #[TestDox('Конструктор вызывает setup() на движке с переданными путями')]
    public function test_constructor_calls_setup_on_engine(): void
    {
        $this->engine->expects($this->once())
            ->method('setup')
            ->with('/templates', '/cache');

        new View($this->engine, '/templates', '/cache');
    }

    #[TestDox('render() делегирует рендер движку и возвращает его результат')]
    public function test_render_delegates_to_engine(): void
    {
        $data = ['title' => 'Hello'];

        $this->engine->method('setup');
        $this->engine->expects($this->once())
            ->method('render')
            ->with('article.tpl', $data)
            ->willReturn('<h1>Hello</h1>');

        $view = new View($this->engine, '/templates', '/cache');

        $result = $view->render('article.tpl', $data);

        self::assertSame('<h1>Hello</h1>', $result);
    }

    #[TestDox('render() без данных передаёт движку пустой массив')]
    public function test_render_defaults_to_empty_data(): void
    {
        $this->engine->method('setup');
        $this->engine->expects($this->once())
            ->method('render')
            ->with('index.tpl', [])
            ->willReturn('');

        $view = new View($this->engine, '/templates', '/cache');

        $view->render('index.tpl');
    }

    #[TestDox('render() с реальным SmartyEngine рендерит шаблон и экранирует переменную name')]
    public function test_render_with_real_smarty_engine_escapes_variable(): void
    {
        $templatesDir = sys_get_temp_dir() . '/smarty-view-test-templates-' . uniqid();
        $cacheDir = sys_get_temp_dir() . '/smarty-view-test-cache-' . uniqid();

        mkdir($templatesDir, recursive: true);
        file_put_contents(
            $templatesDir . '/fixture.tpl',
            '<h1>Hello {$name|escape}, welcome to Smarty!</h1>',
        );

        $view = new View(new SmartyEngine(), $templatesDir, $cacheDir);

        $result = $view->render('fixture.tpl', ['name' => 'Bob & <b>friends</b>']);

        self::assertSame(
            '<h1>Hello Bob &amp; &lt;b&gt;friends&lt;/b&gt;, welcome to Smarty!</h1>',
            $result,
        );

        $this->removeDirectory($templatesDir);
        $this->removeDirectory($cacheDir);
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (new FilesystemIterator($dir) as $item) {
            $item->isDir() ? $this->removeDirectory($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($dir);
    }
}
