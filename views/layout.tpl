<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{block name=title}Blogy{/block}</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-white">
<header class="site-header py-3">
    <div class="container">
        <a href="/" class="brand">Blogy.</a>
    </div>
</header>

<main class="container flex-grow-1 py-5">
    {block name=content}{/block}
</main>

<footer class="site-footer mt-auto">
    <div class="container text-center">
        FOOTER
    </div>
</footer>
</body>
</html>
