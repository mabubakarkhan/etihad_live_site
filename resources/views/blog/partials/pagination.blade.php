{{-- Theme-styled pagination for blog AJAX lists --}}
@if ($paginator->hasPages())
    <div class="pagination-wrap blog-pagination-wrap">
        <div class="pagination float-pagination" role="navigation" aria-label="Blog pagination">
            @if ($paginator->onFirstPage())
                <a href="{{ $paginator->url(1) }}" class="blog-page-link is-disabled" aria-disabled="true" tabindex="-1"><i class="fa-solid fa-chevron-left"></i></a>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="blog-page-link" rel="prev" aria-label="Previous page"><i class="fa-solid fa-chevron-left"></i></a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="blog-page-dots">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <a href="{{ $url }}" class="blog-page-link current-page" aria-current="page">{{ $page }}</a>
                        @else
                            <a href="{{ $url }}" class="blog-page-link">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="blog-page-link" rel="next" aria-label="Next page"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <a href="{{ $paginator->url($paginator->lastPage()) }}" class="blog-page-link is-disabled" aria-disabled="true" tabindex="-1"><i class="fa-solid fa-chevron-right"></i></a>
            @endif
        </div>
    </div>
@endif
