@php
    $phase = $phase ?? null;
    if (!$phase) return;
    $quickStats = $phase->quickStats();
    if ($quickStats === []) return;
@endphp
<section class="dha-quick-stats" aria-label="{{ $phase->title }} quick stats">
    <div class="dha-quick-stats__inner">
        @foreach($quickStats as $index => $item)
            @if($index > 0)
                <span class="dha-quick-stats__divider" aria-hidden="true"></span>
            @endif
            <article class="dha-quick-stats__card">
                <span class="dha-quick-stats__icon" aria-hidden="true">
                    <i data-lucide="{{ $item['icon'] }}"></i>
                </span>
                <strong class="dha-quick-stats__title">{{ $item['title'] }}</strong>
                <span class="dha-quick-stats__text">{{ $item['text'] }}</span>
            </article>
        @endforeach
    </div>
</section>
