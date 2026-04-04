@extends('layouts.front')
@php
    $dealer = $dealer ?? null;
    $cs = $cs ?? \App\Models\ContactSetting::instance();
    $pageTitle = ($dealer && $dealer->meta_title) ? $dealer->meta_title : ($dealer->name . ' – ' . config('app.name'));
    $bannerImage = ($dealer && $dealer->banner_image) ? url('storage/' . ltrim($dealer->banner_image, '/')) : asset('theme/images/bg/6.jpg');
    $dealerAvatar = $dealer && $dealer->profile_pic ? url('storage/' . ltrim($dealer->profile_pic, '/')) : null;
@endphp

@section('title', $pageTitle)

@if($dealer)
@push('meta')
@if(!empty($dealer->meta_description))<meta name="description" content="{{ e($dealer->meta_description) }}">@endif
@if(!empty($dealer->meta_keywords))<meta name="keywords" content="{{ e($dealer->meta_keywords) }}">@endif
@if(!empty($dealer->canonical_url))<link rel="canonical" href="{{ e($dealer->canonical_url) }}">@endif
@endpush
@endif

@section('content')
<div id="main">
    @include('partials.header')

    <div class="wrapper">
        <div class="content">
            <div class="section hero-section hero-section_sin">
                <div class="hero-section-wrap">
                    <div class="hero-section-wrap-item">
                        <div class="container">
                            <div class="hero-section-container">
                                <div class="hero-section-title">
                                    <h2>{{ $dealer->name }}</h2>
                                    <h5>{{ $dealer->info_detail ? \Illuminate\Support\Str::limit(strip_tags($dealer->info_detail), 120) : 'View properties listed by this dealer.' }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="hs-scroll-down-wrap">
                            <div class="scroll-down-item">
                                <div class="mousey"><div class="scroller"></div></div>
                                <span>Scroll Down To Discover</span>
                            </div>
                            <div class="svg-corner svg-corner_white" style="bottom:0;right: -39px; transform: rotate(90deg)"></div>
                            <div class="svg-corner svg-corner_white" style="bottom:0;left: -39px;"></div>
                        </div>
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="{{ $bannerImage }}" data-scrollax="properties: { translateY: '30%' }"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ route('team') }}">Our Team</a>
                    <span>{{ $dealer->name }}</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>

                <div class="main-content ms_vir_height dealer-page">
                    <div class="boxed-container">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="boxed-content btf_init">
                                    <div class="agent-preofile-wrap">
                                        <div class="agent-preofile-header sh-links">
                                            <div class="agent-preofile-header-bg"></div>
                                            <div class="agent-preofile-header-avatar">
                                                <div class="agent-preofile-header-avatar-item">
                                                    @if($dealerAvatar)
                                                    <img src="{{ $dealerAvatar }}" alt="{{ e($dealer->name) }}">
                                                    @else
                                                    <div style="width:100%;height:100%;min-height:180px;background:#cbd5e1;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-user" style="font-size:48px;color:#64748b;"></i></div>
                                                    @endif
                                                    <div class="svg-corner svg-corner_white" style="bottom:18px;right: -45px; transform: rotate(90deg)"></div>
                                                    <div class="svg-corner svg-corner_white" style="bottom:18px;left: -47px;"></div>
                                                </div>
                                            </div>
                                            <div class="abs_bg"></div>
                                            <div class="profile-card-stats">
                                                <ul>
                                                    <li><span>{{ $dealer->properties_count }}</span> Properties</li>
                                                    <li><span>{{ number_format((int) ($dealer->view_count ?? 0)) }}</span> Views</li>
                                                </ul>
                                            </div>
                                            <div class="property-contacts-links">
                                                @php
                                                    $phoneClean = $cs->phone ? preg_replace('/\s+/', '', $cs->phone) : '';
                                                    $whatsappClean = $cs->whatsapp ? preg_replace('/\D/', '', $cs->whatsapp) : $phoneClean;
                                                @endphp
                                                @if($phoneClean)
                                                <a href="tel:{{ $phoneClean }}" class="tolt pcl_btn" data-microtip-position="left" data-tooltip="Call"><i class="fa-solid fa-phone"></i></a>
                                                @endif
                                                @if($whatsappClean)
                                                <a href="https://wa.me/{{ $whatsappClean }}" target="_blank" rel="noopener" class="pcl_btn tolt" data-microtip-position="left" data-tooltip="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                                                @endif
                                                @if($cs->email)
                                                <a href="mailto:{{ e($cs->email) }}" class="pcl_btn tolt" data-microtip-position="left" data-tooltip="Email"><i class="fa-solid fa-envelope"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="agent-preofile-content">
                                            <div class="agent-preofile-content-text">
                                                <h4>{{ $dealer->name }}</h4>
                                                @if($dealer->info_detail)
                                                <p>{!! nl2br(e(strip_tags($dealer->info_detail))) !!}</p>
                                                @else
                                                <p>Properties listed by this dealer. Contact us using the details above for inquiries.</p>
                                                @endif
                                            </div>
                                            @if($dealer->city || $dealer->state)
                                            <div class="tagcloud_single">
                                                <span class="tc_single_title"><i class="fa-regular fa-globe"></i> Area:</span>
                                                <div class="tags-widget">
                                                    @if($dealer->city)<a href="{{ url('/listing') }}?city={{ urlencode($dealer->city) }}">{{ $dealer->city }}</a>@endif
                                                    @if($dealer->state)<a href="{{ url('/listing') }}?state={{ urlencode($dealer->state) }}">{{ $dealer->state }}</a>@endif
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="agent-preofile-footer">
                                            <div class="agent-preofile-footer_title">Follow:</div>
                                            <div class="agent-preofile-footer-social">
                                                @if($cs->facebook)<a href="{{ $cs->facebook }}" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-f"></i></a>@endif
                                                @if($cs->instagram)<a href="{{ $cs->instagram }}" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a>@endif
                                                @if($cs->linkedin)<a href="{{ $cs->linkedin }}" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin-in"></i></a>@endif
                                                @if($cs->youtube)<a href="{{ $cs->youtube }}" target="_blank" rel="noopener"><i class="fa-brands fa-youtube"></i></a>@endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="list-main-wrap-header box-list-header" style="margin-top: 0">
                                    <div class="list-main-wrap-title">
                                        <h2>{{ $dealer->name }} Properties: <strong>{{ $properties->count() }}</strong></h2>
                                    </div>
                                </div>

                                @if($properties->isEmpty())
                                <div class="boxed-content-item" style="padding: 48px 24px; text-align: center;">
                                    <p style="margin: 0; color: #64748b;">No properties listed yet.</p>
                                    <a href="{{ route('listing') }}" class="commentssubmit" style="margin-top: 16px; display: inline-block;">Browse all listings</a>
                                </div>
                                @else
                                <div class="listing-item-container fw-listing-item">
                                    @foreach($properties as $p)
                                    @php
                                        $detailUrl = route('property.show', $p->slug);
                                        $imgUrl = $p->featured_image ? url('storage/' . ltrim($p->featured_image, '/')) : asset('theme/images/all/1.jpg');
                                        $price = format_price($p->price_digits, $p->price_string);
                                        $purposeLabel = $p->purpose === 'rent' ? 'Rent' : 'Sale';
                                        $gallery = is_array($p->gallery) ? $p->gallery : [];
                                        $photoCount = count($gallery) + ($p->featured_image ? 1 : 0);
                                    @endphp
                                    <div class="listing-item">
                                        <div class="geodir-category-listing">
                                            <div class="geodir-category-img">
                                                <a href="{{ $detailUrl }}" class="geodir-category-img_item">
                                                    <div class="bg" style="background-image: url({{ $imgUrl }});"></div>
                                                    <div class="overlay"></div>
                                                </a>
                                                @if($p->short_address)
                                                <div class="geodir-category-location">
                                                    <a href="{{ $detailUrl }}" class="map-item"><i class="fas fa-map-marker-alt"></i> {{ $p->short_address }}</a>
                                                </div>
                                                @endif
                                                <div class="listing-card-cats">
                                                    <ul class="list-single-opt_header_cat">
                                                        <li><a href="{{ route('listing') }}" class="cat-opt">{{ $purposeLabel }}</a></li>
                                                        @foreach($p->projectTypes as $pt)
                                                            <li><a href="{{ route('listing') }}" class="cat-opt">{{ $pt->name }}</a></li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <button type="button" class="geodir_save-btn tolt wishlist-btn" data-property-id="{{ $p->id }}" data-microtip-position="left" data-tooltip="Save" aria-label="Save to wishlist"><span><i class="fa-regular fa-heart wishlist-icon"></i></span></button>
                                                <div class="geodir-category-listing_media-list">
                                                    <span><i class="fas fa-camera"></i> {{ $photoCount }}</span>
                                                </div>
                                            </div>
                                            <div class="geodir-category-content">
                                                <h3><a href="{{ $detailUrl }}">{{ $p->title }}</a></h3>
                                                <div class="geodir-category-content_price">{{ $price }}</div>
                                                @if($p->description)
                                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($p->description), 120) }}</p>
                                                @endif
                                                <div class="geodir-category-content-details">
                                                    <ul>
                                                        <li><i class="fa-light fa-bed"></i><span>{{ $p->bedrooms ?? 0 }}</span></li>
                                                        <li><i class="fa-light fa-bath"></i><span>{{ $p->bathrooms ?? 0 }}</span></li>
                                                        <li><i class="fa-light fa-utensils"></i><span>{{ $p->kitchen ?? 0 }}</span></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="geodir-category-footer">
                                                <span class="gcf-company"><img src="{{ $dealerAvatar ?: asset('theme/images/agents/1.jpg') }}" alt=""><span>By {{ $dealer->name }}</span></span>
                                                <a href="{{ $detailUrl }}" class="gid_link"><span>View Details</span> <i class="fa-solid fa-caret-right"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="limit-box"></div>
                    </div>
                </div>

                <div class="to_top-btn-wrap">
                    <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                    <div class="svg-corner svg-corner_white" style="top:0;left: -40px; transform: rotate(-90deg)"></div>
                    <div class="svg-corner svg-corner_white" style="top:0;right: -40px; transform: rotate(-180deg)"></div>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>

    @include('partials.theme-panels')
</div>

<div id="wishlist-toast" aria-live="polite"></div>
@endsection

@push('styles')
<style>
#wishlist-toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(80px); background: #0f172a; color: #fff; padding: 12px 24px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 9999; opacity: 0; transition: transform 0.3s ease, opacity 0.3s ease; pointer-events: none; font-size: 14px; }
#wishlist-toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
.geodir_save-btn.wishlist-btn { position: absolute; top: 12px; right: 12px; z-index: 2; }
.geodir_save-btn .wishlist-icon { font-size: 1rem; }
.dealer-page .agent-preofile-footer-social a { margin-right: 8px; }
</style>
@endpush

@push('scripts')
<script>
</script>
@endpush
