{extends file='layout.tpl'}

{block name=title}500 — Server Error{/block}

{block name=content}
<section class="text-center py-5">
    <p class="text-uppercase text-muted small mb-2">500</p>
    <h1 class="display-5 mb-3">Something went wrong</h1>
    <p class="lead text-muted mb-4">An unexpected error occurred on the server. We are looking into it.</p>
    <a href="/" class="link-action">Back to homepage</a>
</section>
{/block}
