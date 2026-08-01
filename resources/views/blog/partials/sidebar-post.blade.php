<aside class="blog-sidebar blog-sidebar--single">
    @if (!empty($sidebarCategories) && $sidebarCategories->isNotEmpty())
        @php
            $activeCategoryIds = $post->categories->pluck('id')->all();
        @endphp
        <div class="blog-widget category-widget">
            <h3 class="blog-widget__title">Categories</h3>
            <ul class="cat-item">
                @foreach ($sidebarCategories as $cat)
                    <li>
                        <a href="{{ $cat->url() }}" class="{{ in_array($cat->id, $activeCategoryIds, true) ? 'is-active' : '' }}">{{ $cat->name }}</a>
                        <span>{{ $cat->posts_count }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($post->tags->isNotEmpty())
        <div class="blog-widget tags-widget-wrap">
            <h3 class="blog-widget__title">Tags</h3>
            <div class="tags-widget blog-tags-cloud">
                @foreach ($post->tags as $tag)
                    <a href="{{ $tag->url() }}" class="blog-tag">{{ $tag->name }}</a>
                @endforeach
            </div>
        </div>
    @endif

    @if ($recentInCategory->isNotEmpty())
        <div class="blog-widget recent-post-widget">
            <h3 class="blog-widget__title">
                @if ($post->categories->isNotEmpty())
                    More in {{ $post->categories->pluck('name')->join(', ') }}
                @else
                    Recent Posts
                @endif
            </h3>
            <ul>
                @foreach ($recentInCategory as $related)
                    <li>
                        <div class="recent-post-img">
                            <a href="{{ $related->url() }}">
                                <img src="{{ $related->displayImage() }}" alt="{{ $related->title }}" loading="lazy" width="140" height="100">
                            </a>
                        </div>
                        <div class="recent-post-content">
                            <h4><a href="{{ $related->url() }}" title="{{ $related->title }}">{{ $related->title }}</a></h4>
                            <div class="recent-post-opt">
                                <span class="post-date">
                                    {{ optional($related->published_at)->format('M j, Y') ?: '' }}
                                </span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</aside>
