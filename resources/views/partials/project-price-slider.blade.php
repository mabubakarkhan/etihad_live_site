@php
    $priceSliderImages = is_array($project->price_slider_images ?? null)
        ? array_values(array_filter(array_map(function ($path) {
            return trim((string) $path);
        }, $project->price_slider_images)))
        : [];
    $priceSliderUrls = array_values(array_filter(array_map(function ($path) {
        return asset('storage/' . ltrim($path, '/'));
    }, $priceSliderImages)));
@endphp
@if(count($priceSliderUrls) > 0)
<section class="project-price-slider" id="project-price-slider">
    <div class="container">
        <div class="project-price-slider__frame">
            <div class="project-price-slider__body">
                @if(count($priceSliderUrls) > 1)
                    <div class="project-price-slider__media">
                        <div class="project-price-slider__slider" id="project-price-slider-track">
                            @foreach($priceSliderUrls as $imgUrl)
                                <div class="project-price-slider__slide">
                                    <img src="{{ $imgUrl }}" alt="{{ $project->title }}" loading="lazy">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="project-price-slider__media">
                        <div class="project-price-slider__single">
                            <img src="{{ $priceSliderUrls[0] }}" alt="{{ $project->title }}" loading="lazy">
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif
