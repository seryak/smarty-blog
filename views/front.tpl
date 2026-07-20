{extends file='layout.tpl'}

{block name=content}
{foreach $categories as $category}
    <section class="mb-5 pb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="category-heading mb-0">{$category.title}</h2>
            <a href="/category/{$category.id}" class="link-action">View All</a>
        </div>

        <div class="row g-4">
        {foreach $articlesByCategory[$category.id] as $article}
            <div class="col-md-4">
                <article class="card article-card h-100">
                    <img src="{$article.image}" class="card-img-top" alt="{$article.title}">
                    <div class="card-body px-0">
                        <h3 class="article-title mb-1">
                            <a href="/article/{$article.id}" class="text-reset text-decoration-none">{$article.title}</a>
                        </h3>
                        <p class="article-date mb-2">{$article.published_at}</p>
                        <p class="article-excerpt">{$article.description}</p>
                        <a href="/article/{$article.id}" class="link-action">Continue Reading</a>
                    </div>
                </article>
            </div>
        {/foreach}
        </div>
    </section>
{/foreach}
{/block}
