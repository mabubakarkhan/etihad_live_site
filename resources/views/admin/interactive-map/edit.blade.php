<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Interactive Map · {{ $ownerLabel }} | Etihad Admin</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')
            <main class="flex-1 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 overflow-auto transition-colors">
                <header class="px-6 md:px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between flex-wrap gap-3 sticky top-0 bg-slate-100/95 dark:bg-slate-950/95 z-20">
                    <div>
                        <p class="text-[11px] uppercase tracking-widest text-amber-600 dark:text-amber-400 font-semibold">{{ $ownerType === 'dha-phases' ? 'DHA Phase Map' : 'Interactive Map' }}</p>
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">{{ $ownerLabel }}</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Upload phase overlay, draw plots, edit each cutting. Zoom out hides plot titles; overlay stays.</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        @if(!empty($hubUrl))
                            <a href="{{ $hubUrl }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-amber-500/40 text-amber-700 dark:text-amber-300 hover:bg-amber-500/10 transition">All phases</a>
                        @endif
                        <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Phase editor</a>
                    </div>
                </header>
                <section class="px-4 md:px-6 lg:px-8 py-6 md:py-8">
                    @if($ownerType === 'dha-phases' && !empty($phaseList))
                        <div class="grid gap-4 xl:grid-cols-[240px_minmax(0,1fr)]">
                            <aside class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-3 h-fit sticky top-24">
                                <div class="px-2 pb-2">
                                    <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">DHA phases</p>
                                    <p class="text-xs text-slate-500 mt-1">Click a phase to open its map</p>
                                </div>
                                <input type="search" id="dha-editor-phase-filter" placeholder="Filter…" class="w-full mb-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 px-2.5 py-1.5 text-xs">
                                <div class="space-y-1 max-h-[70vh] overflow-y-auto" id="dha-editor-phase-list">
                                    @foreach($phaseList as $phase)
                                        <a href="{{ $phase['url'] }}"
                                           data-phase-item
                                           data-phase-title="{{ strtolower($phase['title'] . ' ' . $phase['slug']) }}"
                                           class="block rounded-lg px-3 py-2.5 text-sm transition {{ !empty($phase['is_current']) ? 'bg-amber-500/15 border border-amber-500/40 text-amber-800 dark:text-amber-200 font-medium' : 'border border-transparent hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                            <span class="block truncate">{{ $phase['title'] }}</span>
                                            <span class="block text-[10px] text-slate-500 mt-0.5">{{ $phase['status'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </aside>
                            <div>
                                @include('admin.interactive-map._editor', [
                                    'ownerType' => $ownerType,
                                    'ownerId' => $ownerId,
                                    'map' => $map,
                                    'standaloneUrl' => null,
                                ])
                            </div>
                        </div>
                    @else
                        @include('admin.interactive-map._editor', [
                            'ownerType' => $ownerType,
                            'ownerId' => $ownerId,
                            'map' => $map,
                            'standaloneUrl' => null,
                        ])
                    @endif
                </section>
            </main>
        </div>
        <script>
        document.getElementById('dha-editor-phase-filter')?.addEventListener('input', function (e) {
            var q = (e.target.value || '').toLowerCase().trim();
            document.querySelectorAll('#dha-editor-phase-list [data-phase-item]').forEach(function (item) {
                var title = item.getAttribute('data-phase-title') || '';
                item.style.display = (!q || title.indexOf(q) !== -1) ? '' : 'none';
            });
        });
        </script>
    </body>
</html>
