<h1>{$category.title|escape}</h1>
<p>{$category.description|escape}</p>

<ul>
{foreach $articles as $article}
    <li>
        <h3>{$article.title|escape} ({$article.published_at|escape})</h3>
        <img src="{$article.image}">
    </li>
{/foreach}
</ul>
