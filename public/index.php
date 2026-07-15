<?php

declare(strict_types=1);

use App\Core\Router;

require dirname(__DIR__) . '/vendor/autoload.php';

$router = new Router();
$router->dispatch();