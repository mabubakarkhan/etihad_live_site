@php
    $properties = $properties ?? collect();
@endphp

@if($properties->isEmpty())
    <p class="p-3 text-muted mb-0">No items yet.</p>
@else
    <div class="wish-list-items">
        @foreach($properties as $p)
            @php
                $img = $p->featured_image ? url('storage/' . ltrim($p->featured_image, '/')) : asset('theme/images/all/1.jpg');
                $price = format_price($p->price_digits, $p->price_string);
                $detailUrl = url('/property/' . $p->slug);
            @endphp
            <div class="wish-list-item d-flex align-items-center gap-2 etihad-wishlist-row">
                <a href="{{ $detailUrl }}" class="etihad-wishlist-thumb">
                    <img src="{{ $img }}" alt="">
                </a>
                <div class="etihad-wishlist-body">
                    <a href="{{ $detailUrl }}" class="etihad-wishlist-title">
                        {{ $p->title }}
                    </a>
                    <div class="etihad-wishlist-price">
                        {{ $price }}
                    </div>
                </div>
                <button type="button"
                        class="geodir_save-btn tolt wishlist-btn etihad-wishlist-remove"
                        data-property-id="{{ $p->id }}"
                        data-microtip-position="left"
                        data-tooltip="Unsave"
                        aria-label="Remove from wishlist">
                    <span><i class="fa-solid fa-heart wishlist-icon"></i></span>
                </button>
            </div>
        @endforeach
    </div>
@endif
