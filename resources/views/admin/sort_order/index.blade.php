<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Sort display order | Etihad Admin</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
        <style>
            .sortable-ghost { opacity: 0.45; background: rgb(16 185 129 / 0.12); }
            .sortable-drag { cursor: grabbing; }
            .sort-row { cursor: grab; touch-action: none; user-select: none; }
            .sort-row:active { cursor: grabbing; }
            .sort-handle {
                cursor: grab;
                touch-action: none;
                padding: 0.35rem 0.5rem;
                margin: -0.35rem 0;
                border-radius: 0.375rem;
                flex-shrink: 0;
            }
            .sort-handle:hover { background: rgb(148 163 184 / 0.2); }
            .sort-handle:active { cursor: grabbing; }
            #sort-projects-list,
            #sort-properties-list { min-height: 2rem; }
        </style>
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')
            <main class="flex-1 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 overflow-auto transition-colors">
                <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between flex-wrap gap-3 sticky top-0 bg-slate-100/95 dark:bg-slate-950/95 z-10">
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Sort display order</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Drag any row (or the &#9776; grip) to reorder. Top = first on the website.</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Projects</a>
                        <a href="{{ route('admin.own-listings.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Listings</a>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8 space-y-6">
                    <div id="sort-success" class="hidden rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-sm text-emerald-800 dark:text-emerald-200" role="status"></div>
                    <div id="sort-error" class="hidden rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200" role="alert"></div>

                    <div class="flex flex-wrap gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
                        <button type="button" class="sort-tab-btn px-4 py-2 rounded-lg text-sm font-medium transition {{ $activeTab === 'projects' ? 'bg-emerald-500 text-slate-950 shadow shadow-emerald-500/40' : 'border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}" data-tab="projects">Projects ({{ $projects->count() }})</button>
                        <button type="button" class="sort-tab-btn px-4 py-2 rounded-lg text-sm font-medium transition {{ $activeTab === 'listings' ? 'bg-emerald-500 text-slate-950 shadow shadow-emerald-500/40' : 'border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}" data-tab="listings">Listings ({{ $properties->count() }})</button>
                    </div>

                    <div id="panel-projects" class="sort-panel {{ $activeTab === 'projects' ? '' : 'hidden' }}">
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                                <p class="text-sm font-medium text-slate-800 dark:text-slate-200">Projects</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Used on /projects, homepage carousel, footer links, etc.</p>
                            </div>
                            <ul id="sort-projects-list" class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse($projects as $project)
                                    @php $thumb = $project->logo ?? $project->featured_image ?? $project->homepage_listing_image; @endphp
                                    <li class="sort-row flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-900/50 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition" data-id="{{ $project->id }}">
                                        <span class="sort-handle text-slate-400 dark:text-slate-500 select-none" title="Drag to reorder" aria-hidden="true">&#9776;</span>
                                        <span class="sort-position text-xs text-slate-400 dark:text-slate-500 w-8 text-center shrink-0">{{ $loop->iteration }}</span>
                                        @if($thumb)
                                            <img src="{{ asset('storage/' . $thumb) }}" alt="" class="h-10 w-10 object-cover rounded border border-slate-200 dark:border-slate-700 pointer-events-none" draggable="false" />
                                        @else
                                            <span class="inline-flex h-10 w-10 items-center justify-center rounded bg-slate-200 dark:bg-slate-800 text-slate-400 text-xs">—</span>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-900 dark:text-slate-100 truncate">{{ $project->title }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $project->city ?? '—' }} · {{ ucfirst($project->status ?? 'active') }}</p>
                                        </div>
                                    </li>
                                @empty
                                    <li class="px-4 py-8 text-center text-sm text-slate-500">No projects yet.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div id="panel-listings" class="sort-panel {{ $activeTab === 'listings' ? '' : 'hidden' }}">
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                                <p class="text-sm font-medium text-slate-800 dark:text-slate-200">Property listings</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Own and dealer listings — used on listing pages, homepage, dealer pages, etc.</p>
                            </div>
                            <ul id="sort-properties-list" class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse($properties as $property)
                                    <li class="sort-row flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-900/50 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition" data-id="{{ $property->id }}">
                                        <span class="sort-handle text-slate-400 dark:text-slate-500 select-none" title="Drag to reorder" aria-hidden="true">&#9776;</span>
                                        <span class="sort-position text-xs text-slate-400 dark:text-slate-500 w-8 text-center shrink-0">{{ $loop->iteration }}</span>
                                        @if($property->featured_image)
                                            <img src="{{ asset('storage/' . $property->featured_image) }}" alt="" class="h-10 w-14 object-cover rounded border border-slate-200 dark:border-slate-700 pointer-events-none" draggable="false" />
                                        @else
                                            <span class="inline-flex h-10 w-14 items-center justify-center rounded bg-slate-200 dark:bg-slate-800 text-slate-400 text-xs">—</span>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-900 dark:text-slate-100 truncate">{{ $property->title }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $property->city ?? '—' }}
                                                · {{ $property->dealer_id ? ($property->dealer->name ?? 'Dealer') : 'Own' }}
                                                · {{ ucfirst($property->status ?? 'active') }}
                                            </p>
                                        </div>
                                    </li>
                                @empty
                                    <li class="px-4 py-8 text-center text-sm text-slate-500">No listings yet.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 sticky bottom-4">
                        <button type="button" id="save-sort-order" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save display order</button>
                        <span id="sort-save-status" class="text-xs text-slate-500 dark:text-slate-400"></span>
                    </div>
                </section>
            </main>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var csrf = @json(csrf_token());
            var saveUrl = @json(route('admin.sort-order.update'));
            var successEl = document.getElementById('sort-success');
            var errorEl = document.getElementById('sort-error');
            var statusEl = document.getElementById('sort-save-status');

            function showMsg(el, text) {
                if (!el) return;
                el.textContent = text;
                el.classList.remove('hidden');
            }
            function hideMsgs() {
                if (successEl) successEl.classList.add('hidden');
                if (errorEl) errorEl.classList.add('hidden');
            }

            var sortableInstances = {};

            function refreshPositions(listEl) {
                if (!listEl) return;
                listEl.querySelectorAll('.sort-row').forEach(function (row, index) {
                    var pos = row.querySelector('.sort-position');
                    if (pos) pos.textContent = String(index + 1);
                });
            }

            function listIdForTab(tab) {
                return tab === 'listings' ? 'sort-properties-list' : 'sort-projects-list';
            }

            function initSortable(listId) {
                var el = document.getElementById(listId);
                if (!el || typeof Sortable === 'undefined') return null;
                if (!el.querySelector('.sort-row[data-id]')) return null;

                if (sortableInstances[listId]) {
                    sortableInstances[listId].destroy();
                    delete sortableInstances[listId];
                }

                sortableInstances[listId] = Sortable.create(el, {
                    animation: 150,
                    draggable: '.sort-row',
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    forceFallback: true,
                    fallbackOnBody: true,
                    fallbackTolerance: 3,
                    delay: 0,
                    delayOnTouchOnly: true,
                    touchStartThreshold: 3,
                    onSort: function () { refreshPositions(el); }
                });
                return sortableInstances[listId];
            }

            function activateTab(tab) {
                document.querySelectorAll('.sort-tab-btn').forEach(function (b) {
                    var active = b.getAttribute('data-tab') === tab;
                    b.classList.toggle('bg-emerald-500', active);
                    b.classList.toggle('text-slate-950', active);
                    b.classList.toggle('shadow', active);
                    b.classList.toggle('shadow-emerald-500/40', active);
                    b.classList.toggle('border', !active);
                    b.classList.toggle('border-slate-300', !active);
                    b.classList.toggle('dark:border-slate-700', !active);
                    b.classList.toggle('text-slate-700', !active);
                    b.classList.toggle('dark:text-slate-300', !active);
                });
                document.querySelectorAll('.sort-panel').forEach(function (panel) {
                    panel.classList.add('hidden');
                });
                var panel = document.getElementById('panel-' + tab);
                if (panel) panel.classList.remove('hidden');

                requestAnimationFrame(function () {
                    initSortable(listIdForTab(tab));
                });

                if (window.history && window.history.replaceState) {
                    var url = new URL(window.location.href);
                    url.searchParams.set('tab', tab);
                    window.history.replaceState({}, '', url.toString());
                }
            }

            var initialTab = @json($activeTab);
            activateTab(initialTab);

            document.querySelectorAll('.sort-tab-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    activateTab(btn.getAttribute('data-tab'));
                });
            });

            function collectIds(listId) {
                var list = document.getElementById(listId);
                if (!list) return [];
                return Array.prototype.map.call(list.querySelectorAll('.sort-row[data-id]'), function (row) {
                    return parseInt(row.getAttribute('data-id'), 10);
                }).filter(function (id) { return !isNaN(id) && id > 0; });
            }

            var saveBtn = document.getElementById('save-sort-order');
            if (saveBtn) {
                saveBtn.addEventListener('click', function () {
                    hideMsgs();
                    if (statusEl) statusEl.textContent = 'Saving…';
                    saveBtn.disabled = true;
                    var body = new FormData();
                    collectIds('sort-projects-list').forEach(function (id) {
                        body.append('project_ids[]', id);
                    });
                    collectIds('sort-properties-list').forEach(function (id) {
                        body.append('property_ids[]', id);
                    });
                    fetch(saveUrl, {
                        method: 'POST',
                        body: body,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        credentials: 'same-origin'
                    }).then(function (res) {
                        return res.json().then(function (data) {
                            if (res.ok && data.success) {
                                showMsg(successEl, data.message || 'Display order saved.');
                                if (statusEl) statusEl.textContent = '';
                                return;
                            }
                            showMsg(errorEl, (data && data.message) || 'Save failed.');
                            if (statusEl) statusEl.textContent = '';
                        });
                    }).catch(function () {
                        showMsg(errorEl, 'Network error. Please try again.');
                        if (statusEl) statusEl.textContent = '';
                    }).finally(function () {
                        saveBtn.disabled = false;
                    });
                });
            }
        });
        </script>
    </body>
</html>
