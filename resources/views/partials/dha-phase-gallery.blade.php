@php
    $galleryImages = $galleryImages ?? ($phase->galleryImages() ?? []);
    if (count($galleryImages) === 0) return;
@endphp
<section class="dha-phase-gallery" id="dha-gallery" aria-labelledby="dha-phase-gallery-title">
    <div class="dha-phase-gallery__inner">
        <header class="dha-phase-gallery__head">
            <h2 class="dha-phase-gallery__title" id="dha-phase-gallery-title">Gallery</h2>
            <p class="dha-phase-gallery__subtitle">Explore {{ $phase->title }} in pictures</p>
        </header>

        <div class="dha-phase-gallery__grid">
            @foreach($galleryImages as $index => $image)
                <button type="button"
                        class="dha-phase-gallery__item"
                        data-gallery-index="{{ $index }}"
                        aria-label="Open image {{ $index + 1 }} of {{ count($galleryImages) }}">
                    <span class="dha-phase-gallery__frame">
                        <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" loading="lazy" />
                        <span class="dha-phase-gallery__overlay" aria-hidden="true">
                            <i data-lucide="maximize-2"></i>
                        </span>
                    </span>
                </button>
            @endforeach
        </div>
    </div>

    <div class="dha-gallery-lightbox" id="dha-gallery-lightbox" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-label="Image gallery">
        <div class="dha-gallery-lightbox__backdrop" data-gallery-close></div>
        <div class="dha-gallery-lightbox__shell">
            <button type="button" class="dha-gallery-lightbox__close" data-gallery-close aria-label="Close gallery">
                <i data-lucide="x"></i>
            </button>
            <button type="button" class="dha-gallery-lightbox__nav dha-gallery-lightbox__nav--prev" data-gallery-prev aria-label="Previous image">
                <i data-lucide="chevron-left"></i>
            </button>
            <div class="dha-gallery-lightbox__stage">
                <img src="" alt="" class="dha-gallery-lightbox__image" id="dha-gallery-lightbox-image" />
                <span class="dha-gallery-lightbox__counter" id="dha-gallery-lightbox-counter"></span>
            </div>
            <button type="button" class="dha-gallery-lightbox__nav dha-gallery-lightbox__nav--next" data-gallery-next aria-label="Next image">
                <i data-lucide="chevron-right"></i>
            </button>
            <div class="dha-gallery-lightbox__thumbs-wrap">
                <div class="dha-gallery-lightbox__thumbs" id="dha-gallery-lightbox-thumbs">
                    @foreach($galleryImages as $index => $image)
                        <button type="button"
                                class="dha-gallery-lightbox__thumb{{ $index === 0 ? ' is-active' : '' }}"
                                data-gallery-thumb="{{ $index }}"
                                aria-label="View image {{ $index + 1 }}">
                            <img src="{{ $image['url'] }}" alt="" loading="lazy" />
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script type="application/json" id="dha-gallery-data">@json($galleryImages)</script>
</section>
