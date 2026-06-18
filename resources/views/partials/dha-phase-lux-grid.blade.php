@php $phases = $phases ?? collect(); @endphp
<div class="dha-phase-lux-grid">
    @foreach($phases as $phase)
        @php
            $img = $phase->cardImageUrl();
            $titleParts = $phase->heroTitleParts();
            $phaseNum = $phase->cardPhaseNumber();
        @endphp
        <article class="dha-phase-lux-card">
            <a href="{{ route('dha.phase.show', $phase->slug) }}" class="dha-phase-lux-card__link" title="{{ $phase->title }}">
                <div class="dha-phase-lux-card__media">
                    <div class="dha-phase-lux-card__img" style="background-image:url('{{ $img }}')"></div>
                    <span class="dha-phase-lux-card__badge">{{ $phaseNum }}</span>
                    <div class="dha-phase-lux-card__shade" aria-hidden="true"></div>
                    <div class="dha-phase-lux-card__foot">
                        <h3 class="dha-phase-lux-card__title">
                            @if($titleParts['gold'])
                                <span class="dha-phase-lux-card__title-gold">{{ strtoupper($titleParts['gold']) }}</span>
                            @endif
                            @if($titleParts['white'])
                                <span class="dha-phase-lux-card__title-white">{{ strtoupper($titleParts['white']) }}</span>
                            @endif
                        </h3>
                        <span class="dha-phase-lux-card__explore">
                            EXPLORE
                            <i data-lucide="arrow-right" aria-hidden="true"></i>
                        </span>
                    </div>
                    <span class="dha-phase-lux-card__dots" aria-hidden="true"></span>
                </div>
            </a>
        </article>
    @endforeach
</div>
