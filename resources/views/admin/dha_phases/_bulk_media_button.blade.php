@php
    $size = $size ?? 'sm';
    $btnClass = $size === 'md'
        ? 'dha-phase-bulk-media-open inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500 text-slate-950 border border-emerald-500 hover:bg-emerald-400 transition shadow shadow-emerald-500/30'
        : 'dha-phase-bulk-media-open text-[10px] leading-tight px-1.5 py-0.5 rounded font-semibold bg-emerald-500 text-slate-950 border border-emerald-500 hover:bg-emerald-400 transition';
@endphp
<button
    type="button"
    class="{{ $btnClass }}"
    data-phase-id="{{ $phase->id }}"
    data-phase-title="{{ $phase->title }}"
    data-preview-url="{{ route('admin.dha-phases.bulk-media.preview', $phase) }}"
    data-import-url="{{ route('admin.dha-phases.bulk-media.import', $phase) }}"
>Bulk Media</button>
