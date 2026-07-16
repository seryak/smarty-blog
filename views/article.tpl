<h1>{$article.title|escape}</h1>
<p>{$article.description|escape}</p>

<img src="{$article.image}">

<div>{$article.text|escape}</div>

<footer>
    <span>Просмотров: {$article.views|escape}</span>
    <span>Опубликовано: {$article.published_at|escape}</span>
</footer>
