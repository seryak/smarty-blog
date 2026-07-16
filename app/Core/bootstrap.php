<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Container;
use App\Core\SmartyEngine;
use App\Core\TemplateEngine;
use App\Core\View;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/../vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__) . '/..')->safeLoad();

$viewConfig = require dirname(__DIR__) . '/../config/view.php';
$databaseConfig = require dirname(__DIR__) . '/../config/database.php';

$container = new Container();

$container->bind(TemplateEngine::class, fn () => new SmartyEngine());

$container->bind(View::class, fn (Container $c) => new View(
    $c->get(TemplateEngine::class),
    $viewConfig['templates_dir'],
    $viewConfig['cache_dir'],
));

return new App($container, $_SERVER['REQUEST_URI']);
