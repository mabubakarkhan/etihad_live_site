<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Edit {{ $sections[$section]['label'] ?? $section }} · {{ $property->title }} | Etihad Admin</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        @include('admin.projects._tom_select_dark')
        @include('admin.projects._vertical_tabs_style')
        @include('admin.partials.icon_picker')
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        @php
            $citiesByState = $states->keyBy('name')->map(fn($s) => $s->cities->pluck('name')->toArray())->toArray();
        @endphp
        <script>window.citiesByState = @json($citiesByState);</script>
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')
            <main class="flex-1 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 overflow-auto transition-colors">
                <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">{{ $pageTitle }} · {{ $sections[$section]['label'] ?? $section }}</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $property->title }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route($routePrefix . '.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Back to list</a>
                        <a href="{{ route($routePrefix . '.edit', $property) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-sky-500/50 text-sky-600 dark:text-sky-400 hover:bg-sky-500/10 transition">Full editor</a>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8">
                    <div id="section-success-top" class="mb-4 hidden rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-sm text-emerald-800 dark:text-emerald-200" role="status"></div>
                    <div id="form-errors-top" class="mb-4 hidden rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200" role="alert">
                        <p class="font-medium mb-1">Please fix the following:</p>
                        <ul id="form-errors-list" class="list-disc list-inside space-y-0.5"></ul>
                    </div>
                    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
                        <nav class="project-vertical-tabs lg:w-56 flex-shrink-0 border border-slate-200 dark:border-slate-800 rounded-xl bg-white dark:bg-slate-900/80 p-2 shadow-sm lg:sticky lg:top-24 self-start" id="section-tab-nav">
                            <div class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider px-2 py-1.5 mb-1">Sections</div>
                            @foreach($sections as $slug => $meta)
                                <button type="button"
                                    class="section-tab-btn w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $slug === $section ? 'active' : '' }}"
                                    data-section="{{ $slug }}"
                                    data-load-url="{{ route($routePrefix . '.edit-section.load', [$property, $slug]) }}">
                                    {{ $meta['label'] }}
                                </button>
                            @endforeach
                        </nav>
                        <div class="flex-1 min-w-0">
                            <form method="POST"
                                id="section-edit-form"
                                class="admin-section-edit-form"
                                data-entity="property"
                                data-section="{{ $section }}"
                                data-update-url="{{ route($routePrefix . '.sections.update', [$property, $section]) }}"
                                data-upload-url="{{ route($routePrefix . '.upload-media') }}"
                                data-entity-id="{{ $property->id }}"
                                data-property-id="{{ $property->id }}"
                                data-full-edit-url="{{ route($routePrefix . '.edit', $property) }}"
                                data-full-update-url="{{ route($routePrefix . '.update', $property) }}">
                                @csrf
                                @method('PATCH')
                                <div class="mb-4 flex flex-wrap items-center gap-3">
                                    <button type="submit" class="section-save-btn inline-flex items-center justify-center rounded-lg bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save section</button>
                                    <button type="button" class="full-save-btn inline-flex items-center justify-center rounded-lg border border-emerald-600/60 px-6 py-2.5 text-sm font-semibold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-500/10 transition">Open full editor to save all</button>
                                    <span id="section-save-status" class="text-xs text-slate-500 dark:text-slate-400"></span>
                                </div>
                                <div id="section-content">
                                    @include('admin.properties._section_fragment', compact('property', 'listingType', 'projectTypes', 'dealers', 'states', 'section'))
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </main>
        </div>
        <script src="{{ asset('theme/js/admin-property-media.js') }}"></script>
        <script src="{{ asset('theme/js/admin-section-edit.js') }}"></script>
        @include('admin.properties._form_scripts')
    </body>
</html>
