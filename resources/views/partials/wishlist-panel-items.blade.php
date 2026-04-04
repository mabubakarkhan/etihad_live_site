@php
    $properties = $properties ?? collect();
@endphp

<style>
    /* Wishlist panel: remove the theme's vertical divider line inside items */
    .wish-list-items .wish-list-item:before { display: none !important; }
    /* Wishlist panel: ensure FontAwesome solid heart actually renders filled */
    .wish-list-wrap .wishlist-icon.fa-solid { font-weight: 900 !important; }
    .wish-list-wrap .wishlist-icon.fa-regular { font-weight: 400 !important; }
</style>

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
            <div class="wish-list-item d-flex align-items-center gap-2" style="position:relative;padding:12px 16px;border-bottom:1px solid #eef2f7;">
                <a href="{{ $detailUrl }}" style="display:block;width:54px;height:46px;flex:0 0 54px;overflow:hidden;border-radius:8px;background:#e2e8f0;">
                    <img src="{{ $img }}" alt="" style="width:100%;height:100%;object-fit:cover;display:block;">
                </a>
                <div style="min-width:0;flex:1 1 auto;">
                    <a href="{{ $detailUrl }}" style="display:block;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $p->title }}
                    </a>
                    <div style="font-size:13px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $price }}
                    </div>
                </div>
                <button type="button"
                        class="geodir_save-btn tolt wishlist-btn"
                        data-property-id="{{ $p->id }}"
                        data-microtip-position="left"
                        data-tooltip="Unsave"
                        aria-label="Remove from wishlist"
                        style="position:static;top:auto;right:auto;">
                    <span><i class="fa-solid fa-heart wishlist-icon"></i></span>
                </button>
            </div>
        @endforeach
    </div>
@endif

