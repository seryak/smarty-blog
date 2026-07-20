{extends file='layout.tpl'}

{block name=title}{$category.title}{/block}

{block name=content}
<div class="mb-4">
    <h1 class="h3">{$category.title}</h1>
    <p class="text-secondary">{$category.description}</p>
</div>

<div class="d-flex flex-wrap gap-2 mb-4">
    <span class="align-self-center small text-muted me-2">Сортировать:</span>
    <a href="/category/{$category.id}?sort=published_at&dir=desc" class="btn btn-sm btn-outline-primary">по дате (новые)</a>
    <a href="/category/{$category.id}?sort=published_at&dir=asc" class="btn btn-sm btn-outline-primary">по дате (старые)</a>
    <a href="/category/{$category.id}?sort=views&dir=desc" class="btn btn-sm btn-outline-primary">по просмотрам</a>
</div>

<div class="row g-4">
{foreach $articles as $article}
    <div class="col-md-4">
        <article class="card article-card h-100">
            <img src="{$article.image}" class="card-img-top" alt="{$article.title}">
            <div class="card-body px-0">
                <h3 class="article-title mb-1">
                    <a href="/article/{$article.id}" class="text-reset text-decoration-none">{$article.title}</a>
                </h3>
                <p class="article-date mb-2">{$article.published_at}, просмотров: {$article.views}</p>
            </div>
        </article>
    </div>
{foreachelse}
    <p class="text-muted">В этой категории пока нет статей.</p>
{/foreach}
</div>

{if $paginator->totalPages() > 1}
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item {if !$paginator->hasPrev()}disabled{/if}">
                <a class="page-link" href="/category/{$category.id}?sort={$sort}&dir={$direction}&page={$paginator->page-1}">← Назад</a>
            </li>
            <li class="page-item disabled">
                <span class="page-link">Страница {$paginator->page} из {$paginator->totalPages()}</span>
            </li>
            <li class="page-item {if !$paginator->hasNext()}disabled{/if}">
                <a class="page-link" href="/category/{$category.id}?sort={$sort}&dir={$direction}&page={$paginator->page+1}">Вперёд →</a>
            </li>
        </ul>
    </nav>
{/if}
{/block}
