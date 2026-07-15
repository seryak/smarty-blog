<?php

declare(strict_types=1);

namespace App\Core;

final class UrlParser
{
    /**
     * @return list<string>
     */
    public function parse(string $requestUri): array
    {
        $path = trim((string) parse_url($requestUri, PHP_URL_PATH), '/');

        return $path === '' ? [] : explode('/', $path);
    }
}
