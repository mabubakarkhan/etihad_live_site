<div id="blog-ajax-root" class="blog-ajax-root" data-blog-ajax="1">
    <div id="blog-posts-ajax" class="blog-posts-ajax">
        @include('blog.partials.posts-list', ['posts' => $posts])
    </div>
    <div class="blog-ajax-loader" id="blog-ajax-loader" hidden aria-hidden="true">
        <span class="blog-ajax-loader__spin"></span>
        <span>Loading…</span>
    </div>
</div>
