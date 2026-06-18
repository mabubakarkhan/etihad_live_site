@php
    $imageUrl = $project->homepage_listing_image
        ? url('storage/' . ltrim($project->homepage_listing_image, '/'))
        : ($project->featured_image ? url('storage/' . ltrim($project->featured_image, '/')) : asset('theme/images/all/1.jpg'));

    $pricingCards = is_array($project->pricing_place_cards ?? null) ? array_values($project->pricing_place_cards) : [];
    $startingFrom = '—';
    if (!empty($pricingCards[0]['price']) && trim((string) $pricingCards[0]['price']) !== '') {
        $startingFrom = trim((string) $pricingCards[0]['price']);
    } elseif ($project->price !== null && $project->price !== '') {
        $startingFrom = is_numeric($project->price)
            ? config('app.currency', 'PKR') . ' ' . number_format((float) $project->price, 0)
            : $project->price;
    }

    $launchYear = (int) ($project->launch_year ?? 2023);
    if ($launchYear < 1900) {
        $launchYear = 2023;
    }

    $locationLine = trim((string) (
        $project->short_address
        ?: $project->full_address
        ?: implode(', ', array_filter([trim((string) ($project->city ?? '')), trim((string) ($project->state ?? ''))]))
    ));

    $cs = \App\Models\ContactSetting::instance();
    $waRaw = trim((string) ($cs->whatsapp ?? '')) ?: trim((string) ($cs->phone ?? ''));
    $waNumber = $waRaw !== '' ? preg_replace('/\D/', '', $waRaw) : '';
    $waText = urlencode('Hi, I would like to register my interest in ' . $project->title);
    $waUrl = $waNumber !== '' ? 'https://wa.me/' . $waNumber . '?text=' . $waText : route('project.show', $project->slug);
@endphp
<div class="listing-item portal-project-card-item">
    <article class="portal-project-card geodir-category-listing">
        <div class="geodir-category-img">
            <a href="{{ route('project.show', $project->slug) }}" class="geodir-category-img_item">
                <div class="bg" data-bg="{{ $imageUrl }}"></div>
                <div class="overlay"></div>
            </a>
            @if($locationLine !== '')
            <div class="geodir-category-location">
                <a href="{{ route('project.show', $project->slug) }}" class="map-item">
                    <i class="fas fa-map-marker-alt"></i> {{ \Illuminate\Support\Str::limit($locationLine, 42, '...') }}
                </a>
            </div>
            @endif
            @if($project->projectTypes->isNotEmpty())
            <ul class="list-single-opt_header_cat">
                @foreach($project->projectTypes as $pt)
                <li><a href="{{ url('/projects') }}?project_type={{ urlencode($pt->slug) }}" class="cat-opt">{{ $pt->name }}</a></li>
                @endforeach
            </ul>
            @endif
        </div>
        <div class="portal-project-card-body">
            <h3 class="portal-project-card-title">
                <a href="{{ route('project.show', $project->slug) }}">{{ $project->title }}</a>
            </h3>
            <div class="portal-project-card-stats">
                <div class="portal-project-card-stat">
                    <span class="portal-project-card-stat-label">Starting From</span>
                    <strong class="portal-project-card-stat-value">{{ $startingFrom }}</strong>
                </div>
                <div class="portal-project-card-stat">
                    <span class="portal-project-card-stat-label">Launch Year</span>
                    <strong class="portal-project-card-stat-value">{{ $launchYear }}</strong>
                </div>
            </div>
            <a href="{{ $waUrl }}" class="portal-project-card-wa" target="_blank" rel="noopener noreferrer">
                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                <span>Register Interest</span>
            </a>
        </div>
    </article>
</div>
