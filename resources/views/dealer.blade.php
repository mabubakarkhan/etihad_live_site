@extends('layouts.front')
@php
    $dealer = $dealer ?? null;
    $cs = $cs ?? \App\Models\ContactSetting::instance();
    $pageTitle = ($dealer && $dealer->meta_title) ? $dealer->meta_title : ($dealer->name . ' – ' . config('app.name'));
    $bannerImage = ($dealer && $dealer->banner_image) ? url('storage/' . ltrim($dealer->banner_image, '/')) : asset('theme/images/bg/6.jpg');
    $dealerAvatar = $dealer && $dealer->profile_pic ? url('storage/' . ltrim($dealer->profile_pic, '/')) : null;
    $heroImage = $dealerAvatar ?: $bannerImage;
    $dealerEmail = $dealer->email ?: ($cs->email ?? null);
    $dealerPhone = $dealer->phone ?: ($dealer->mobile ?: ($cs->phone ?? null));
    $dealerWhatsapp = $dealer->whatsapp ?: ($cs->whatsapp ?? $dealerPhone);
    $phoneClean = $dealerPhone ? preg_replace('/\s+/', '', $dealerPhone) : '';
    $whatsappClean = $dealerWhatsapp ? preg_replace('/\D/', '', $dealerWhatsapp) : '';
    $activeProperties = (int) ($dealer->properties_count ?? $properties->count());
    $locationParts = array_filter([$dealer->city ?? null, $dealer->state ?? null]);
    $aboutText = $dealer->info_detail ? trim(strip_tags($dealer->info_detail)) : '';
    $socialLinks = array_filter([
        'facebook' => $cs->facebook ?? null,
        'instagram' => $cs->instagram ?? null,
        'linkedin' => $cs->linkedin ?? null,
        'youtube' => $cs->youtube ?? null,
        'twitter' => $cs->twitter ?? null,
        'tiktok' => $cs->tiktok ?? null,
    ]);
@endphp

@section('title', $pageTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => seo_from_record($dealer, [
    'title' => $pageTitle,
    'description' => $aboutText,
    'canonical' => url()->current(),
    'image' => $heroImage,
])])
@endpush

@section('content')
<div id="main">
    @include('partials.header')

    <div class="wrapper">
        <div class="content dealer-profile-page">
            <section class="dealer-profile-hero">
                <div class="dealer-profile-hero-media">
                    <img src="{{ $heroImage }}" alt="" class="dealer-profile-hero-bg dealer-profile-hero-bg-desktop" loading="eager" aria-hidden="true">
                    <img src="{{ $bannerImage }}" alt="" class="dealer-profile-hero-bg dealer-profile-hero-bg-mobile" loading="eager" aria-hidden="true">
                    <div class="dealer-profile-hero-overlay"></div>
                </div>
                <div class="container dealer-profile-hero-body">
                    <div class="dealer-profile-hero-inner">
                        <div class="dealer-profile-hero-main">
                            <div class="dealer-profile-avatar">
                                @if($dealerAvatar)
                                <img src="{{ $dealerAvatar }}" alt="{{ e($dealer->name) }}" loading="lazy">
                                @else
                                <span class="dealer-profile-avatar-fallback"><i class="fa-solid fa-user"></i></span>
                                @endif
                            </div>
                            <div class="dealer-profile-meta">
                                <h1 class="dealer-profile-name">{{ $dealer->name }}</h1>
                                <div class="dealer-profile-meta-details">
                                <a href="{{ route('portal') }}" class="dealer-profile-company">{{ config('app.name') }} <i class="fa-solid fa-angle-right"></i></a>
                                <p class="dealer-profile-stat"><strong>{{ $activeProperties }}</strong> Active {{ $activeProperties === 1 ? 'Property' : 'Properties' }}</p>
                                @if(!empty($locationParts))
                                <div class="dealer-profile-tags">
                                    @foreach($locationParts as $loc)
                                    <span class="dealer-profile-tag"><i class="fa-solid fa-location-dot"></i> {{ $loc }}</span>
                                    @endforeach
                                    @if($dealer->show_homepage)
                                    <span class="dealer-profile-tag dealer-profile-tag-featured"><i class="fa-solid fa-star"></i> Featured Agent</span>
                                    @endif
                                </div>
                                @elseif($dealer->show_homepage)
                                <div class="dealer-profile-tags">
                                    <span class="dealer-profile-tag dealer-profile-tag-featured"><i class="fa-solid fa-star"></i> Featured Agent</span>
                                </div>
                                @endif
                                </div>
                            </div>
                        </div>
                        <div class="dealer-profile-actions">
                            @if($dealerEmail)
                            <a href="mailto:{{ e($dealerEmail) }}" class="dealer-profile-action dealer-profile-action-email" title="Email">
                                <i class="fa-solid fa-envelope"></i>
                                <span>Email</span>
                            </a>
                            @endif
                            @if($phoneClean)
                            <a href="tel:{{ $phoneClean }}" class="dealer-profile-action dealer-profile-action-call" title="Call">
                                <i class="fa-solid fa-phone"></i>
                                <span>Call</span>
                            </a>
                            @endif
                            @if($whatsappClean)
                            <a href="https://wa.me/{{ $whatsappClean }}" target="_blank" rel="noopener" class="dealer-profile-action dealer-profile-action-whatsapp" title="WhatsApp">
                                <i class="fa-brands fa-whatsapp"></i>
                                <span>WhatsApp</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <div class="container">
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ route('team') }}">Our Team</a>
                    <span>{{ $dealer->name }}</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>

                <div class="main-content dealer-page">
                    <div class="dealer-profile-about">
                        <h2>About {{ $dealer->name }}</h2>
                        @if($aboutText !== '')
                        <p>{!! nl2br(e($aboutText)) !!}</p>
                        @else
                        <p>Properties listed by this team member. Use the contact buttons above for inquiries about available listings.</p>
                        @endif
                        @if($dealer->address)
                        <p class="dealer-profile-about-line"><i class="fa-solid fa-map-marker-alt"></i> {{ $dealer->address }}</p>
                        @endif
                        @if(!empty($socialLinks))
                        <div class="dealer-profile-social">
                            <span class="dealer-profile-social-label">Follow:</span>
                            @if(!empty($socialLinks['facebook']))<a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>@endif
                            @if(!empty($socialLinks['instagram']))<a href="{{ $socialLinks['instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>@endif
                            @if(!empty($socialLinks['linkedin']))<a href="{{ $socialLinks['linkedin'] }}" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>@endif
                            @if(!empty($socialLinks['youtube']))<a href="{{ $socialLinks['youtube'] }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>@endif
                            @if(!empty($socialLinks['twitter']))<a href="{{ $socialLinks['twitter'] }}" target="_blank" rel="noopener" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>@endif
                            @if(!empty($socialLinks['tiktok']))<a href="{{ $socialLinks['tiktok'] }}" target="_blank" rel="noopener" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>@endif
                        </div>
                        @endif
                    </div>

                    <div class="dealer-profile-listings">
                        <div class="dealer-profile-listings-head">
                            <h2>{{ $dealer->name }} Properties: <strong>{{ $properties->count() }}</strong></h2>
                        </div>

                        @if($properties->isEmpty())
                        <div class="dealer-profile-empty">
                            <p>No properties listed yet.</p>
                            <a href="{{ route('listing') }}" class="commentssubmit">Browse all listings</a>
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
                                            <div class="bg" data-bg="{{ $imgUrl }}"></div>
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
                                        <h3 class="listing-card-title"><a href="{{ $detailUrl }}">{{ $p->title }}</a></h3>
                                        <div class="geodir-category-content_price">{{ $price }}</div>
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

                <div class="to_top-btn-wrap">
                    <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                    <div class="svg-corner svg-corner_white hero-corner-tl"></div>
                    <div class="svg-corner svg-corner_white hero-corner-tr"></div>
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
<link rel="stylesheet" href="{{ asset('theme/css/pages/dealer.css') }}">
@endpush
