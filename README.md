### 1. Поднять контейнеры

```bash
docker compose up -d --build
```

Поднимутся `nginx` (порт **8080**), `php-fpm` и `mysql` (порт **3316**).
Схема БД применяется автоматически при первой инициализации mysql (из `bin/db.sql`).

### 2. Настроить `.env`

```bash
cp .env.example .env
```

Для Docker в `.env` укажите хост и пароль контейнерного MySQL:

```dotenv
DB_HOST=mysql
DB_PORT=3306
DB_NAME=smarty_blog
DB_USER=root
DB_PASSWORD=root

VIEW_TEMPLATES_DIR=views
VIEW_CACHE_DIR=cache/smarty
```

### 3. Установить зависимости и наполнить БД

```bash
docker compose exec php composer install
docker compose exec php php bin/seed.php
```

`bin/seed.php` создаёт 6 категорий и 25 статей («Статья 1..25») со случайными категориям.

### 4. Собрать стили

```bash
npm install
npm run css        
```

### 5. Открыть

**http://localhost:8080**

---


```bash
# Пересоздать БД
docker compose exec php php bin/db.php
docker compose exec php php bin/seed.php
```
