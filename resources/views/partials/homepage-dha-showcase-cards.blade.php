@foreach($phases as $phase)
@php
    $imageUrl = $phase->cardImageUrl();
    $tag = 'DHA';
    if (! empty($phase->stat_location)) {
        $tag = strtoupper(trim(explode(',', (string) $phase->stat_location)[0]));
    }
    $excerpt = '';
    if (! empty($phase->description)) {
        $excerpt = \Illuminate\Support\Str::limit(strip_tags((string) $phase->description), 120);
    } elseif (! empty($phase->hero_lead)) {
        $excerpt = \Illuminate\Support\Str::limit(strip_tags((string) $phase->hero_lead), 120);
    }
    $metaOne = trim((string) ($phase->stat_total_plots ?? ''));
    if ($metaOne !== '') {
        $metaOne = $metaOne . (str_contains(strtolower($metaOne), 'plot') ? '' : ' Plots');
    }
    $metaTwo = trim((string) ($phase->stat_total_area ?? ''));
    if ($metaTwo === '' && ! empty($phase->stat_year_developed)) {
        $metaTwo = 'Est. ' . trim((string) $phase->stat_year_developed);
    }
@endphp
                <article class="dha-showcase__card">
                  <div class="dha-showcase__card-media">
                    <img src="{{ $imageUrl }}" alt="{{ $phase->title }} preview" loading="lazy" />
                  </div>
                  <div class="dha-showcase__card-content">
                    <span class="dha-showcase__card-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="dha-showcase__tag">{{ $tag }}</span>
                    <h3 class="dha-showcase__card-title">
                      <a href="{{ route('dha.phase.show', $phase->slug) }}">{{ $phase->title }}</a>
                    </h3>
                    @if($excerpt !== '')
                    <p class="dha-showcase__card-text">{{ $excerpt }}</p>
                    @endif
                    @if($metaOne !== '' || $metaTwo !== '')
                    <div class="dha-showcase__meta">
                      @if($metaOne !== '')
                      <span class="dha-showcase__meta-item">{{ $metaOne }}</span>
                      @endif
                      @if($metaTwo !== '')
                      <span class="dha-showcase__meta-item">{{ $metaTwo }}</span>
                      @endif
                    </div>
                    @endif
                  </div>
                </article>
@endforeach
