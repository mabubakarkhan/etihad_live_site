@php
    $phase = $phase ?? null;
    if (! $phase) {
        return;
    }

    $landmarks = $phase->nearbyLandmarks();
    if ($landmarks === []) {
        return;
    }

    $mapEmbedUrl = $phase->mapEmbedUrl();
@endphp
<section class="dha-landmarks" id="dha-nearby-landmarks" aria-labelledby="dha-landmarks-title">
    <div class="dha-landmarks__inner">
        <header class="dha-landmarks__head">
            <span class="dha-landmarks__eyebrow">Accessibility</span>
            <h2 class="dha-landmarks__title" id="dha-landmarks-title">Nearby Attractions &amp; Landmarks</h2>
            <p class="dha-landmarks__lead">Key connections and destinations that make {{ $phase->title }} more informative and convenient.</p>
        </header>

        <div class="dha-landmarks__layout">
            <div class="dha-landmarks__cards">
                @foreach($landmarks as $item)
                    <article class="dha-landmarks__card">
                        <span class="dha-landmarks__icon" aria-hidden="true">
                            <i data-lucide="{{ $item['icon'] }}"></i>
                        </span>
                        <div class="dha-landmarks__copy">
                            <strong class="dha-landmarks__card-title">{{ $item['title'] }}</strong>
                            <span class="dha-landmarks__card-text">{{ $item['text'] }}</span>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="dha-landmarks__map">
                @if($mapEmbedUrl)
                    <iframe
                        src="{{ $mapEmbedUrl }}"
                        title="{{ $phase->title }} accessibility map"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                    ></iframe>
                @else
                    <div class="dha-landmarks__map-fallback">Map will appear when location coordinates are added.</div>
                @endif
            </div>
        </div>
    </div>
</section>
