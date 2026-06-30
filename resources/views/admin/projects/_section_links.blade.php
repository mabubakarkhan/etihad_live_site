@php
    $projectSections = $projectSections ?? \App\Support\ProjectEditSections::all();
@endphp
<div class="flex flex-wrap gap-1 max-w-md">
    @foreach($projectSections as $slug => $meta)
        <a href="{{ route('admin.projects.edit-section', [$project, $slug]) }}" class="text-[10px] leading-tight px-1.5 py-0.5 rounded border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">{{ $meta['label'] }}</a>
    @endforeach
</div>
