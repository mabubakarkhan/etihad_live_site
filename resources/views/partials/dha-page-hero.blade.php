@php
    $heroImage = $heroImage ?? asset('theme/images/bg/6.jpg');
    $heroAlt = $heroAlt ?? '';
@endphp
<section class="dha-page-hero">
    <img src="{{ $heroImage }}" alt="{{ $heroAlt }}" class="dha-page-hero-img" loading="eager" />
    <div class="dha-page-hero-overlay" aria-hidden="true"></div>
    <div class="container dha-page-hero-body">
        {{ $slot ?? '' }}
    </div>
</section>
