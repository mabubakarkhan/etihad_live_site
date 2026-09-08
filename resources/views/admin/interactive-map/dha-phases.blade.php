<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DHA Phase Maps | Etihad Admin</title>
    @include('admin.partials.theme-init')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen">
<div class="min-h-screen flex">
    @include('admin.partials.sidebar')
    <main class="flex-1 overflow-auto">
        <header class="px-6 md:px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-wrap gap-3 sticky top-0 bg-slate-100/95 dark:bg-slate-950/95 z-20">
            <div>
                <p class="text-[11px] uppercase tracking-widest text-amber-600 dark:text-amber-400 font-semibold">Live GIS</p>
                <h1 class="text-xl md:text-2xl font-semibold tracking-tight">DHA Phase Maps</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Select a phase to open its map, upload the overlay, then draw and edit plots.</p>
            </div>
            <div class="flex items-center gap-2">
                @include('admin.partials.theme-toggle')
                <a href="{{ route('admin.dha-phases.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Manage phases</a>
            </div>
        </header>

        <section class="p-6 md:p-8">
            @if($phases->isEmpty())
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-10 text-center">
                    <p class="text-slate-500">No DHA phases found yet.</p>
                    <a href="{{ route('admin.dha-phases.create') }}" class="inline-flex mt-4 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950">Add a phase</a>
                </div>
            @else
                <div class="mb-4 flex items-center justify-between gap-3 flex-wrap">
                    <p class="text-sm text-slate-500">{{ $phases->count() }} phase{{ $phases->count() === 1 ? '' : 's' }} · click any card to open that map</p>
                    <input type="search" id="dha-map-phase-filter" placeholder="Filter phases…" class="w-full max-w-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm">
                </div>
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" id="dha-map-phase-grid">
                    @foreach($phases as $phase)
                        @php
                            $map = $phase->interactiveMap;
                            $hasOverlay = $map && $map->hasOverlay();
                            $plotCount = $map ? $map->sections->count() : 0;
                        @endphp
                        <a href="{{ route('admin.dha-phases.interactive-map', $phase) }}"
                           data-phase-card
                           data-phase-title="{{ strtolower($phase->title . ' ' . $phase->slug) }}"
                           class="group rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 hover:border-amber-500/50 hover:shadow-lg hover:shadow-amber-500/10 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[11px] uppercase tracking-wider text-slate-500">Phase {{ $phase->sort_order }}</p>
                                    <h2 class="text-lg font-semibold mt-1 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">{{ $phase->title }}</h2>
                                    <p class="text-xs text-slate-500 mt-1">{{ $phase->slug }}</p>
                                </div>
                                <span class="text-[11px] px-2 py-0.5 rounded {{ $phase->status === 'active' ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-slate-500/20 text-slate-500' }}">{{ $phase->status }}</span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2 text-[11px]">
                                <span class="rounded-full px-2.5 py-1 {{ $hasOverlay ? 'bg-amber-500/15 text-amber-700 dark:text-amber-300' : 'bg-slate-500/10 text-slate-500' }}">
                                    {{ $hasOverlay ? 'Overlay ready' : 'No overlay yet' }}
                                </span>
                                <span class="rounded-full px-2.5 py-1 bg-sky-500/10 text-sky-700 dark:text-sky-300">
                                    {{ $plotCount }} plot{{ $plotCount === 1 ? '' : 's' }}
                                </span>
                            </div>
                            <p class="mt-4 text-sm font-medium text-amber-700 dark:text-amber-400 group-hover:underline">Open map editor →</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
</div>
<script>
document.getElementById('dha-map-phase-filter')?.addEventListener('input', function (e) {
    var q = (e.target.value || '').toLowerCase().trim();
    document.querySelectorAll('[data-phase-card]').forEach(function (card) {
        var title = card.getAttribute('data-phase-title') || '';
        card.style.display = (!q || title.indexOf(q) !== -1) ? '' : 'none';
    });
});
</script>
</body>
</html>
