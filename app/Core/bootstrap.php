<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Container;
use App\Core\Database;
use App\Core\Router;
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

$container->bind(PDO::class, function () use ($databaseConfig) {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $databaseConfig['host'],
        $databaseConfig['port'],
        $databaseConfig['dbname'],
    );

    return new PDO($dsn, $databaseConfig['user'], $databaseConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
});

$container->bind(Database::class, fn (Container $c) => new Database($c->get(PDO::class)));

$action = (new Router())->resolve($_SERVER['REQUEST_URI']);

return new App($container, $action);
