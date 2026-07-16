<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);

return [
    'templates_dir' => $projectRoot . '/' . ($_ENV['VIEW_TEMPLATES_DIR'] ?? throw new RuntimeException('VIEW_TEMPLATES_DIR is not set in .env')),
    'cache_dir' => $projectRoot . '/' . ($_ENV['VIEW_CACHE_DIR'] ?? throw new RuntimeException('VIEW_CACHE_DIR is not set in .env')),
];
