@php $phases = $phases ?? collect(); @endphp
@if($phases->isNotEmpty())
<div class="dha-phase-carousel-wrap">
    <div class="dha-phase-carousel">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach($phases as $phase)
                    @php $img = $phase->cardImageUrl(); @endphp
                    <div class="swiper-slide">
                        <a href="{{ route('dha.phase.show', $phase->slug) }}" class="dha-phase-card" title="{{ $phase->title }}">
                            <div class="dha-phase-card-bg" style="background-image:url('{{ $img }}')"></div>
                            <div class="dha-phase-card-overlay"></div>
                            <span class="dha-phase-card-title">{{ $phase->title }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="dha-phase-button dha-phase-button-next"><i class="fas fa-caret-right"></i></div>
        <div class="dha-phase-button dha-phase-button-prev"><i class="fas fa-caret-left"></i></div>
    </div>
</div>
@endif
