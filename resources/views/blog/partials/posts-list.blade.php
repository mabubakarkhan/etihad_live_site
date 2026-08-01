@if ($posts->isEmpty())
    <div class="blog-empty">
        <p>No posts found.</p>
    </div>
@else
    <div class="post-items fl-wrap blog-posts-grid">
        @foreach ($posts as $post)
            @php
                $img = $post->displayImage();
                $primaryCat = $post->categories->first();
            @endphp
            <article class="post-item blog-card">
                <div class="post-item_media">
                    <a href="{{ $post->url() }}" class="blog-card__media-link" aria-label="{{ $post->title }}">
                        <img src="{{ $img }}" alt="{{ $post->title }}" loading="lazy" width="640" height="400">
                    </a>
                    @if ($primaryCat)
                        <div class="post_header_cat">
                            <a href="{{ $primaryCat->url() }}">{{ $primaryCat->name }}</a>
                        </div>
                    @endif
                </div>
                <div class="post-item_content">
                    <div class="blog-card__meta">
                        @if ($post->published_at)
                            <span><i class="fa-regular fa-calendar"></i> {{ $post->published_at->format('M j, Y') }}</span>
                        @endif
                        <span><i class="fa-regular fa-user"></i> {{ optional($post->author)->name ?: 'Admin' }}</span>
                    </div>
                    <h3><a href="{{ $post->url() }}">{{ $post->title }}</a></h3>
                    <p class="blog-card__excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?: $post->content), 280) }}</p>
                    <a class="post-card_link" href="{{ $post->url() }}">Read More <i class="fa-solid fa-caret-right"></i></a>
                </div>
            </article>
        @endforeach
    </div>
    {{ $posts->links('blog.partials.pagination') }}
@endif
