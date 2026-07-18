<h1>{$category.title|escape}</h1>
<p>{$category.description|escape}</p>

<nav>
    Сортировать:
    <a href="/category/{$category.id}?sort=published_at&dir=desc">по дате (новые)</a>
    <a href="/category/{$category.id}?sort=published_at&dir=asc">по дате (старые)</a>
    <a href="/category/{$category.id}?sort=views&dir=desc">по просмотрам</a>
</nav>

<ul>
{foreach $articles as $article}
    <li>
        <h3><a href="/article/{$article.id}">{$article.title|escape}</a></h3>
        <span>{$article.published_at|escape}, просмотров: {$article.views|escape}</span>
        <span><img src="{$article.image}"></span>
    </li>
{foreachelse}
    <li>В этой категории пока нет статей.</li>
{/foreach}
</ul>
