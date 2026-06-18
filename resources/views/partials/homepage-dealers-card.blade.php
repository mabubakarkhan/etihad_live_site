@php
    $badgeText = $badge ?? 'Trusted Agent';

    $hasImage = ! empty($dealer->profile_pic);
    $imageUrl = $hasImage
        ? url('storage/' . ltrim($dealer->profile_pic, '/'))
        : (! empty($dealer->banner_image)
            ? url('storage/' . ltrim($dealer->banner_image, '/'))
            : asset('theme/images/all/1.jpg'));

    $location = trim(implode(', ', array_filter([
        trim((string) ($dealer->city ?? '')),
        trim((string) ($dealer->state ?? '')),
    ])));

    $excerpt = $dealer->info_detail
        ? \Illuminate\Support\Str::limit(strip_tags($dealer->info_detail), 120)
        : '';

    $propsCount = (int) ($dealer->properties_count ?? 0);
    $viewsCount = (int) ($dealer->view_count ?? 0);
    $highlight = $propsCount === 1 ? '1 Property' : number_format($propsCount) . ' Properties';
    $ctaLabel = $ctaLabel ?? 'View profile';
@endphp
                  <article class="popular-listings__card">
                    <div class="popular-listings__media">
                      <span class="popular-listings__badge">{{ $badgeText }}</span>
                      <img src="{{ $imageUrl }}" alt="{{ $dealer->name }} profile" loading="lazy" />
                    </div>
                    <div class="popular-listings__body">
                      @if($location !== '')
                      <p class="popular-listings__location">{{ $location }}</p>
                      @endif
                      <h3 class="popular-listings__title">
                        <a href="{{ route('dealer.show', $dealer->slug) }}">{{ $dealer->name }}</a>
                      </h3>
                      <p class="popular-listings__price">{{ $highlight }}</p>
                      @if($excerpt !== '')
                      <p class="popular-listings__text">{{ $excerpt }}</p>
                      @endif
                      <div class="popular-listings__meta">
                        <div><span class="popular-listings__meta-value">{{ number_format($propsCount) }}</span><span class="popular-listings__meta-label">Properties</span></div>
                        <div><span class="popular-listings__meta-value">{{ number_format($viewsCount) }}</span><span class="popular-listings__meta-label">Views</span></div>
                        <div><span class="popular-listings__meta-value">{{ $location !== '' ? $location : 'Etihad' }}</span><span class="popular-listings__meta-label">Location</span></div>
                      </div>
                      <a href="{{ route('dealer.show', $dealer->slug) }}" class="popular-listings__cta">{{ $ctaLabel }}</a>
                    </div>
                  </article>
