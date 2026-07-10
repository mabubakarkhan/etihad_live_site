@php
    $size = $size ?? 'sm';
    $btnClass = $size === 'md'
        ? 'project-bulk-media-open inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500 text-slate-950 border border-emerald-500 hover:bg-emerald-400 transition shadow shadow-emerald-500/30'
        : 'project-bulk-media-open text-[10px] leading-tight px-1.5 py-0.5 rounded font-semibold bg-emerald-500 text-slate-950 border border-emerald-500 hover:bg-emerald-400 transition';
@endphp
<button
    type="button"
    class="{{ $btnClass }}"
    data-project-id="{{ $project->id }}"
    data-project-title="{{ $project->title }}"
    data-preview-url="{{ route('admin.projects.bulk-media.preview', $project) }}"
    data-import-url="{{ route('admin.projects.bulk-media.import', $project) }}"
>Bulk Media</button>
