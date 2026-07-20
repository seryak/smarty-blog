#!/bin/sh
set -e

# автосоздание папки кеша, иначе приложение не может создать папку и крашится
mkdir -p /var/www/html/cache/smarty/compile /var/www/html/cache/smarty/cache
chmod -R 0777 /var/www/html/cache

exec "$@"
