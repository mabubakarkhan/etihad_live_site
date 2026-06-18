@php
    $imageUrl = $property->featured_image
        ? url('storage/' . ltrim($property->featured_image, '/'))
        : asset('theme/images/all/1.jpg');

    $location = trim((string) (
        $property->short_address
        ?: $property->address
        ?: implode(', ', array_filter([trim((string) ($property->town ?? '')), trim((string) ($property->city ?? ''))]))
    ));

    $excerpt = $property->description
        ? \Illuminate\Support\Str::limit(strip_tags($property->description), 120)
        : '';

    $price = format_price($property->price_digits, $property->price_string);

    $areaParts = [];
    if ($property->area_kanal) {
        $areaParts[] = rtrim(rtrim(number_format((float) $property->area_kanal, 2), '0'), '.') . ' Kanal';
    }
    if ($property->area_marla) {
        $areaParts[] = rtrim(rtrim(number_format((float) $property->area_marla, 2), '0'), '.') . ' Marla';
    }
    $areaLabel = $areaParts ? implode(' / ', $areaParts) : null;

    $purposeLabel = $property->purpose === \App\Models\Property::PURPOSE_RENT ? 'Rent' : 'Sale';
    $typeLabel = $property->property_type
        ? ucfirst(str_replace('_', ' ', (string) $property->property_type))
        : ($property->projectTypes->first()?->name ?? 'Listing');

    $badgeText = $badge
        ?? ($property->is_hot ? 'Featured' : ($property->projectTypes->first()?->name ?? $purposeLabel));
@endphp
                  <article class="popular-listings__card">
                    <div class="popular-listings__media">
                      <span class="popular-listings__badge">{{ $badgeText }}</span>
                      <img src="{{ $imageUrl }}" alt="{{ $property->title }} listing preview" loading="lazy" />
                    </div>
                    <div class="popular-listings__body">
                      @if($location !== '')
                      <p class="popular-listings__location">{{ $location }}</p>
                      @endif
                      <h3 class="popular-listings__title">
                        <a href="{{ route('property.show', $property->slug) }}">{{ $property->title }}</a>
                      </h3>
                      <p class="popular-listings__price">{{ $price }}</p>
                      @if($excerpt !== '')
                      <p class="popular-listings__text">{{ $excerpt }}</p>
                      @endif
                      <div class="popular-listings__meta">
                        @if($areaLabel)
                        <div><span class="popular-listings__meta-value">{{ $areaLabel }}</span><span class="popular-listings__meta-label">Plot Size</span></div>
                        @endif
                        <div><span class="popular-listings__meta-value">{{ $purposeLabel }}</span><span class="popular-listings__meta-label">Purpose</span></div>
                        <div><span class="popular-listings__meta-value">{{ $typeLabel }}</span><span class="popular-listings__meta-label">Type</span></div>
                      </div>
                      <a href="{{ route('property.show', $property->slug) }}" class="popular-listings__cta">View listing</a>
                    </div>
                  </article>
