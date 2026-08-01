<aside class="blog-sidebar">
    <div class="blog-widget category-widget">
        <h3 class="blog-widget__title">Categories</h3>
        <ul class="cat-item">
            @foreach ($categories as $cat)
                <li>
                    <a href="{{ $cat->url() }}" class="{{ !empty($activeCategory) && $cat->id === $activeCategory->id ? 'is-active' : '' }}">{{ $cat->name }}</a>
                    <span>{{ $cat->posts_count }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    @if (!empty($tags) && $tags->isNotEmpty())
        <div class="blog-widget tags-widget-wrap">
            <h3 class="blog-widget__title">Tags</h3>
            <div class="tags-widget blog-tags-cloud">
                @foreach ($tags as $tag)
                    <a href="{{ $tag->url() }}" class="blog-tag {{ !empty($activeTag) && $tag->id === $activeTag->id ? 'is-active' : '' }}">{{ $tag->name }}</a>
                @endforeach
            </div>
        </div>
    @endif
</aside>
