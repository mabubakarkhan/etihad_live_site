{{-- Single property card for More Properties section (grid or slider) --}}
@php $p = $p ?? []; $listingBase = $listing_base ?? url('/listing'); @endphp
<div class="listing-item {{ $p['filter_class'] ?? '' }}">
    <div class="geodir-category-listing">
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
        </div>
        <div class="geodir-category-content">
            <h3><a href="{{ $p['detail_url'] ?? '#' }}">{{ $p['title'] ?? '' }}</a></h3>
            <div class="geodir-category-content_price">{{ $p['price'] ?? '' }}</div>
            @if(!empty($p['excerpt']))
            <p>{{ $p['excerpt'] }}</p>
            @endif
            <div class="geodir-category-content-details">
                <ul>
                    <li><i class="fa-light fa-bed"></i><span>{{ $p['bedrooms'] ?? 0 }}</span></li>
                    <li><i class="fa-light fa-bath"></i><span>{{ $p['bathrooms'] ?? 0 }}</span></li>
                    <li><i class="fa-light fa-utensils"></i><span>{{ $p['kitchen'] ?? 0 }}</span></li>
                </ul>
            </div>
        </div>
        <div class="geodir-category-footer">
            <span class="gcf-company"><img src="{{ $p['dealer_image_url'] ?? asset('theme/images/avatar/1.jpg') }}" alt=""><span>By {{ $p['dealer_name'] ?? 'Etihad Marketing' }}</span></span>
            <a href="{{ $p['detail_url'] ?? '#' }}" class="gid_link"><span>View Details</span> <i class="fa-solid fa-caret-right"></i></a>
        </div>
    </div>
</div>
