{extends file='layout.tpl'}

{block name=title}{$article.title}{/block}

{block name=content}
<article class="mx-auto" style="max-width: 800px;">
    <h1 class="h2 mb-2">{$article.title}</h1>
    <p class="article-date mb-4">
        {$article.published_at} · просмотров: {$article.views}
    </p>

    <img src="{$article.image}" class="img-fluid rounded mb-4 w-100" alt="{$article.title}">

    <p class="lead text-secondary">{$article.description}</p>

    <div class="fs-6 lh-lg">{$article.text}</div>
</article>

{if $similar}
<section class="mt-5 pt-4 border-top">
    <h2 class="category-heading mb-4">Похожие статьи</h2>
    <div class="row g-4">
    {foreach $similar as $item}
        <div class="col-md-4">
            <article class="card article-card h-100">
                <img src="{$item.image}" class="card-img-top" alt="{$item.title}">
                <div class="card-body px-0">
                    <h3 class="article-title mb-1">
                        <a href="/article/{$item.id}" class="text-reset text-decoration-none">{$item.title}</a>
                    </h3>
                    <p class="article-date mb-0">{$item.published_at}</p>
                </div>
            </article>
        </div>
    {/foreach}
    </div>
</section>
{/if}
{/block}
