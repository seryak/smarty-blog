{foreach $categories as $category}
    <h2>{$category.title|escape}</h2>
    <ul>
    {foreach $articlesByCategory[$category.id] as $article}
        <li>
            <h3>{$article.title|escape} ({$article.published_at|escape})  ({$article.id})</h3>
            <img src="{$article.image}">
        </li>
    {/foreach}
    </ul>
{/foreach}
