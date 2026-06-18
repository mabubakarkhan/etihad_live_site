<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Our Achievements | Etihad Admin</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')

            <main class="flex-1 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 transition-colors">
                <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Our Achievements</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Statistics grid on the root homepage — heading and stat cards.</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ url('/') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">View homepage</a>
                    </div>
                </header>

                <section class="px-6 md:px-8 py-6 md:py-8 space-y-6">
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.homepage-achievements.update') }}" class="space-y-6 max-w-3xl" id="stats-form">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Section heading</h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="title_line_1" class="block text-slate-700 dark:text-slate-300">Title (main)</label>
                                    <input id="title_line_1" name="title_line_1" type="text" value="{{ old('title_line_1', $setting->title_line_1) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="title_highlight" class="block text-slate-700 dark:text-slate-300">Title (teal highlight)</label>
                                    <input id="title_highlight" name="title_highlight" type="text" value="{{ old('title_highlight', $setting->title_highlight) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-amber-500/30 bg-amber-500/5 dark:bg-amber-500/10 p-4 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong class="text-slate-800 dark:text-slate-100">Stat cards</strong> — Responsive auto-fit grid (min ~<strong>250×120 px</strong> per card). Value shows large in gold; optional suffix (<code class="text-[11px]">+</code>, <code class="text-[11px]">%</code>) renders smaller beside the number.
                        </div>

                        <div id="stats-list" class="space-y-4">
                            @php $oldStats = old('stats', $stats->map(fn ($s) => ['id' => $s->id, 'value' => $s->value, 'suffix' => $s->suffix, 'label' => $s->label])->all()); @endphp
                            @foreach ($oldStats as $index => $stat)
                                <div class="stat-row rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Stat <span class="stat-number-label">{{ $index + 1 }}</span></h2>
                                        <button type="button" class="remove-stat text-xs text-rose-600 dark:text-rose-400 hover:underline" @if(count($oldStats) <= 1) hidden @endif>Remove</button>
                                    </div>
                                    @if(!empty($stat['id']))
                                        <input type="hidden" name="stats[{{ $index }}][id]" value="{{ $stat['id'] }}" />
                                    @endif
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div class="space-y-1.5">
                                            <label class="block text-slate-700 dark:text-slate-300">Value</label>
                                            <input name="stats[{{ $index }}][value]" type="text" value="{{ $stat['value'] ?? '' }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="5000" />
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="block text-slate-700 dark:text-slate-300">Suffix (optional)</label>
                                            <input name="stats[{{ $index }}][suffix]" type="text" value="{{ $stat['suffix'] ?? '' }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="+ or %" />
                                        </div>
                                        <div class="space-y-1.5 sm:col-span-1">
                                            <label class="block text-slate-700 dark:text-slate-300">Label</label>
                                            <input name="stats[{{ $index }}][label]" type="text" value="{{ $stat['label'] ?? '' }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="add-stat" class="inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">+ Add stat</button>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save achievements section</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>

        <template id="stat-template">
            <div class="stat-row rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Stat <span class="stat-number-label"></span></h2>
                    <button type="button" class="remove-stat text-xs text-rose-600 dark:text-rose-400 hover:underline">Remove</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-slate-700 dark:text-slate-300">Value</label>
                        <input type="text" required class="stat-value block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-700 dark:text-slate-300">Suffix (optional)</label>
                        <input type="text" class="stat-suffix block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-700 dark:text-slate-300">Label</label>
                        <input type="text" required class="stat-label-input block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                    </div>
                </div>
            </div>
        </template>

        <script>
            (function () {
                const list = document.getElementById('stats-list');
                const template = document.getElementById('stat-template');
                const addBtn = document.getElementById('add-stat');
                const maxStats = 12;

                function reindex() {
                    const rows = list.querySelectorAll('.stat-row');
                    rows.forEach((row, index) => {
                        row.querySelector('.stat-number-label').textContent = index + 1;
                        const value = row.querySelector('.stat-value') || row.querySelector('input[name*="[value]"]');
                        const suffix = row.querySelector('.stat-suffix') || row.querySelector('input[name*="[suffix]"]');
                        const label = row.querySelector('.stat-label-input') || row.querySelector('input[name*="[label]"]');
                        if (value) value.name = `stats[${index}][value]`;
                        if (suffix) suffix.name = `stats[${index}][suffix]`;
                        if (label) label.name = `stats[${index}][label]`;
                        const idInput = row.querySelector('input[name*="[id]"]');
                        if (idInput) idInput.name = `stats[${index}][id]`;
                        const removeBtn = row.querySelector('.remove-stat');
                        if (removeBtn) removeBtn.hidden = rows.length <= 1;
                    });
                    addBtn.disabled = rows.length >= maxStats;
                    addBtn.classList.toggle('opacity-50', rows.length >= maxStats);
                }

                addBtn.addEventListener('click', function () {
                    if (list.querySelectorAll('.stat-row').length >= maxStats) return;
                    list.appendChild(template.content.cloneNode(true));
                    reindex();
                });

                list.addEventListener('click', function (event) {
                    if (!event.target.classList.contains('remove-stat')) return;
                    if (list.querySelectorAll('.stat-row').length <= 1) return;
                    event.target.closest('.stat-row').remove();
                    reindex();
                });

                reindex();
            })();
        </script>
    </body>
</html>
