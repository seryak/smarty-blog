<?php

declare(strict_types=1);

use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\Controller\ErrorPageController;
use App\Controller\FrontPageController;
use App\Core\App;
use App\Core\Container;
use App\Core\ControllerFactory;
use App\Core\Database;
use App\Core\Request;
use App\Core\Router;
use App\Core\SmartyEngine;
use App\Core\TemplateEngine;
use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
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
$container->bind(ArticleRepository::class, fn (Container $c) => new ArticleRepository($c->get(Database::class)));
$container->bind(CategoryRepository::class, fn (Container $c) => new CategoryRepository($c->get(Database::class)));
$container->bind(Request::class, fn () => new Request($_GET, $_SERVER));

$container->bind(FrontPageController::class, fn (Container $c) => new FrontPageController(
    $c->get(CategoryRepository::class),
    $c->get(ArticleRepository::class),
    $c->get(View::class),
));

$container->bind(ArticleController::class, fn (Container $c) => new ArticleController(
    $c->get(ArticleRepository::class),
    $c->get(View::class),
));

$container->bind(CategoryController::class, fn (Container $c) => new CategoryController(
    $c->get(CategoryRepository::class),
    $c->get(ArticleRepository::class),
    $c->get(Request::class),
    $c->get(View::class),
));

$container->bind(ErrorPageController::class, fn (Container $c) => new ErrorPageController(
    $c->get(View::class),
));

$route = (new Router($container->get(Request::class)))->resolve();
$action = (new ControllerFactory($container))->create($route);

return new App($action, $container->get(View::class));
