@php $phases = $phases ?? collect(); @endphp
<div class="dha-phase-grid">
    @foreach($phases as $phase)
        @php $img = $phase->cardImageUrl(); @endphp
        <a href="{{ route('dha.phase.show', $phase->slug) }}" class="dha-phase-card" title="{{ $phase->title }}">
            <div class="dha-phase-card-bg" style="background-image:url('{{ $img }}')"></div>
            <div class="dha-phase-card-overlay"></div>
            <span class="dha-phase-card-title">{{ $phase->title }}</span>
        </a>
    @endforeach
</div>
