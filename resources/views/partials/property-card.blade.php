{{-- Single property card for More Properties section (grid or slider) --}}
@php
    $p = $p ?? [];
    $listingBase = $listing_base ?? url('/listing');
    $cs = \App\Models\ContactSetting::instance();
    $waRaw = trim((string) ($cs->whatsapp ?? '')) ?: trim((string) ($cs->phone ?? ''));
    $waNumber = $waRaw !== '' ? preg_replace('/\D/', '', $waRaw) : '';
    $waText = urlencode('Hi, I am interested in ' . ($p['title'] ?? 'this property'));
    $waUrl = $waNumber !== '' ? 'https://wa.me/' . $waNumber . '?text=' . $waText : ($p['detail_url'] ?? '#');
    $dealerName = trim((string) ($p['dealer_name'] ?? ''));
    $dealerImageUrl = trim((string) ($p['dealer_image_url'] ?? ''));
    $dealerUrl = trim((string) ($p['dealer_url'] ?? ''));
    if ($dealerUrl === '' && !empty($p['dealer_slug'])) {
        $dealerUrl = route('dealer.show', $p['dealer_slug']);
    }
    $showDealerAvatar = $dealerName !== '' && $dealerImageUrl !== '';
@endphp
<div class="listing-item etihad-property-card-item">
    <div class="geodir-category-listing etihad-property-card">
        <div class="geodir-category-img">
            <a href="{{ $p['detail_url'] ?? '#' }}" class="geodir-category-img_item">
                <div class="bg" style="background-image:url({{ $p['featured_image_url'] ?? '' }})"></div>
                <div class="overlay"></div>
            </a>
            @if(!empty($p['short_address']))
            <div class="geodir-category-location">
                <a href="{{ $p['detail_url'] ?? '#' }}" class="map-item"><i class="fas fa-map-marker-alt"></i> {{ $p['short_address'] }}</a>
            </div>
            @endif
            <ul class="list-single-opt_header_cat">
                @if(!empty($p['purpose_label']))
                <li><a href="{{ $listingBase }}?purpose={{ $p['purpose_label'] === 'Rent' ? 'rent' : 'sale' }}" class="cat-opt">{{ $p['purpose_label'] }}</a></li>
                @endif
                @if(!empty($p['property_type']))
                <li><a href="{{ $listingBase }}?property_type={{ urlencode($p['property_type']) }}" class="cat-opt">{{ $p['property_type'] }}</a></li>
                @endif
                @foreach($p['project_type_names'] ?? [] as $ptName)
                <li><a href="{{ $listingBase }}" class="cat-opt">{{ $ptName }}</a></li>
                @endforeach
            </ul>
            @if(!empty($p['id']))
            <button type="button" class="geodir_save-btn tolt wishlist-btn" data-property-id="{{ $p['id'] }}" data-microtip-position="left" data-tooltip="Save" aria-label="Save to wishlist"><span><i class="fa-regular fa-heart wishlist-icon"></i></span></button>
            @else
            <a href="{{ $p['detail_url'] ?? '#' }}" class="geodir_save-btn tolt" data-microtip-position="left" data-tooltip="Save" aria-label="Save"><span><i class="fal fa-heart"></i></span></a>
            @endif
            <div class="geodir-category-listing_media-list">
                <span><i class="fas fa-camera"></i> {{ $p['photo_count'] ?? 0 }}</span>
            </div>
            @if($showDealerAvatar)
            @if($dealerUrl !== '')
            <a href="{{ $dealerUrl }}" class="listing-card-dealer-avatar" aria-label="{{ $dealerName }}">
                <img src="{{ $dealerImageUrl }}" alt="{{ $dealerName }}" loading="lazy" decoding="async">
                <span class="listing-card-dealer-tooltip">{{ $dealerName }}</span>
            </a>
            @else
            <span class="listing-card-dealer-avatar" title="{{ $dealerName }}">
                <img src="{{ $dealerImageUrl }}" alt="{{ $dealerName }}" loading="lazy" decoding="async">
            </span>
            @endif
            @endif
        </div>
        <div class="etihad-property-card-body">
            <h3 class="listing-card-title"><a href="{{ $p['detail_url'] ?? '#' }}">{{ $p['title'] ?? '' }}</a></h3>
            <div class="listing-card-meta-row">
                <div class="geodir-category-content_price listing-card-meta-price">{{ $p['price'] ?? '' }}</div>
                <a href="{{ $p['detail_url'] ?? '#' }}" class="listing-card-view-details gid_link"><span>View Details</span> <i class="fa-solid fa-caret-right"></i></a>
            </div>
            <a href="{{ $waUrl }}" class="portal-project-card-wa" target="_blank" rel="noopener noreferrer">
                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                <span>Register Interest</span>
            </a>
        </div>
    </div>
</div>
