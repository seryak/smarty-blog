<h1>Articles</h1>
<ul>
{foreach $articles as $article}
    <li>
        <h3>{$article.title|escape} ({$article.published_at|escape})</h3>
        <img src="{$article.image}">
    </li>
{/foreach}
</ul>
