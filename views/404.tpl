{extends file='layout.tpl'}

{block name=title}404 — Not Found{/block}

{block name=content}
<section class="text-center py-5">
    <p class="text-uppercase text-muted small mb-2">404</p>
    <h1 class="display-5 mb-3">Page not found</h1>
    <p class="lead text-muted mb-4">{$message}</p>
    <a href="/" class="link-action">Back to homepage</a>
</section>
{/block}
