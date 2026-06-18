@props([
    'name',
    'path' => null,
    'accept' => 'image/*',
    'kind' => 'image',
    'pathName' => null,
    'removeName' => null,
    'previewClass' => 'max-h-48 rounded-lg border border-slate-200 dark:border-slate-600 object-cover',
])

@php
    $pathName = $pathName ?? ($name . '_path');
    $removeName = $removeName ?? ('remove_' . $name);
    $currentPath = old($pathName, $path);
    $previewUrl = $currentPath ? public_storage_url($currentPath) : null;
@endphp

<div
    data-homepage-media-wrap
    data-remove-name="{{ $removeName }}"
    @if($previewClass) data-preview-class="{{ $previewClass }}" @endif
>
    <input type="hidden" name="{{ $pathName }}" value="{{ $currentPath }}" />

    <div data-homepage-media-preview class="{{ $previewUrl ? '' : 'hidden' }} mb-3">
        @if($previewUrl)
            @if($kind === 'video')
                <video src="{{ $previewUrl }}" class="max-h-40 rounded-lg border border-slate-200 dark:border-slate-600" controls muted></video>
            @else
                <img src="{{ $previewUrl }}" alt="Preview" class="{{ $previewClass }}" />
            @endif
            <label class="mt-3 inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer">
                <input type="checkbox" name="{{ $removeName }}" value="1" class="rounded border-slate-400" /> Remove current file
            </label>
        @endif
    </div>

    <input
        type="file"
        accept="{{ $accept }}"
        class="homepage-media-upload block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200"
        data-upload-type="{{ $name }}"
        data-path-name="{{ $pathName }}"
        data-media-kind="{{ $kind }}"
    />

    <p data-homepage-media-status class="mt-2 text-xs text-slate-500 dark:text-slate-400"></p>
</div>
