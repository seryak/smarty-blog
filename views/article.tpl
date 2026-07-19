<h1>{$article.title|escape}</h1>
<p>{$article.description|escape}</p>

<img src="{$article.image}">

<div>{$article.text|escape}</div>

<footer>
    <span>Просмотров: {$article.views|escape}</span>
    <span>Опубликовано: {$article.published_at|escape}</span>
</footer>

{if $similar}
<section>
    <h2>Похожие статьи</h2>
    <ul>
    {foreach $similar as $item}
        <li><a href="/article/{$item.id}">{$item.title|escape}</a></li>
    {/foreach}
    </ul>
</section>
{/if}
