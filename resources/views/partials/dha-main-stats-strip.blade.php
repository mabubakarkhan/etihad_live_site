@php
    $dha = $dha ?? \App\Models\DhaSetting::instance();
    $stats = $dha->heroStats();
@endphp
@if(count($stats))
<div class="dha-main-stats-strip" aria-label="DHA Lahore highlights">
    <div class="dha-main-stats-strip__inner dha-main-stats-strip__inner--count-{{ count($stats) }}">
        @foreach($stats as $stat)
            <article class="dha-main-stats-strip__item">
                <span class="dha-main-stats-strip__icon" aria-hidden="true">
                    <i data-lucide="{{ $stat['icon'] }}"></i>
                </span>
                <strong class="dha-main-stats-strip__value">{{ $stat['value'] }}</strong>
                <span class="dha-main-stats-strip__label">{{ $stat['label'] }}</span>
            </article>
        @endforeach
    </div>
</div>
@endif
