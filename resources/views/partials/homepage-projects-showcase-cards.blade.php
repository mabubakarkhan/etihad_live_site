@foreach($projects as $project)
@php
    $imageUrl = $project->homepage_listing_image
        ? url('storage/' . ltrim($project->homepage_listing_image, '/'))
        : ($project->featured_image ? url('storage/' . ltrim($project->featured_image, '/')) : asset('theme/images/all/1.jpg'));

    $tag = trim((string) ($project->city ?? ''));
    if ($tag === '' && $project->projectTypes->isNotEmpty()) {
        $tag = $project->projectTypes->first()->name;
    }
    if ($tag === '') {
        $tag = 'Project';
    }

    $excerpt = $project->description
        ? \Illuminate\Support\Str::limit(strip_tags($project->description), 120)
        : '';

    $pricingCards = is_array($project->pricing_place_cards ?? null) ? array_values($project->pricing_place_cards) : [];
    $metaOne = '';
    $metaTwo = '';

    if (!empty($pricingCards[0]['price']) && trim((string) $pricingCards[0]['price']) !== '') {
        $metaOne = trim((string) $pricingCards[0]['price']);
    } elseif ($project->price !== null && $project->price !== '') {
        $metaOne = is_numeric($project->price)
            ? config('app.currency', 'PKR') . ' ' . number_format((float) $project->price, 0)
            : (string) $project->price;
    }

    if (!empty($pricingCards[0]['label']) && trim((string) $pricingCards[0]['label']) !== '') {
        $metaOne = trim((string) $pricingCards[0]['label']) . ($metaOne !== '' ? ': ' . $metaOne : '');
    }

    $launchYear = (int) ($project->launch_year ?? 0);
    if ($launchYear >= 1900) {
        $metaTwo = 'Launch ' . $launchYear;
    } elseif ($project->projectTypes->isNotEmpty()) {
        $metaTwo = $project->projectTypes->first()->name;
    }

    if ($metaOne === '' && $metaTwo !== '') {
        $metaOne = $metaTwo;
        $metaTwo = '';
    }
@endphp
                <a href="{{ route('project.show', $project->slug) }}" class="dha-showcase__card">
                  <div class="dha-showcase__card-media">
                    <img src="{{ $imageUrl }}" alt="{{ $project->title }} preview" loading="lazy" />
                  </div>
                  <div class="dha-showcase__card-content">
                    <span class="dha-showcase__card-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="dha-showcase__tag">{{ $tag }}</span>
                    <h3 class="dha-showcase__card-title">{{ $project->title }}</h3>
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
                </a>
@endforeach
