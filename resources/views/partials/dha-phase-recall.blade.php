@php
    $phase = $phase ?? null;
    if (!$phase) {
        return;
    }

    $recallHeading = trim((string) ($phase->recall_heading ?? ''))
        ?: ('Houses for Rent ' . $phase->title);
    $recallLabel = trim((string) ($phase->recall_map_label ?? '')) ?: 'Download Map';
    $recallCta = trim((string) ($phase->recall_cta_text ?? '')) ?: 'Download Map';
    $pdfUrl = $phase->phasePdfUrl();
    $downloadName = \Illuminate\Support\Str::slug($phase->title ?: 'dha-phase') . '-map.pdf';
@endphp
@if($pdfUrl)
<section class="dha-recall" id="dha-recall" aria-labelledby="dha-recall-title">
    <div class="dha-recall__inner">
        <div class="dha-recall__copy">
            <h2 class="dha-recall__title" id="dha-recall-title">{{ $recallHeading }}</h2>
            <p class="dha-recall__label">{{ $recallLabel }}</p>
        </div>
        <a
            href="{{ $pdfUrl }}"
            class="dha-recall__cta"
            download="{{ $downloadName }}"
            target="_blank"
            rel="noopener noreferrer"
        >
            <i data-lucide="download" aria-hidden="true"></i>
            <span>{{ $recallCta }}</span>
        </a>
    </div>
</section>
@endif
