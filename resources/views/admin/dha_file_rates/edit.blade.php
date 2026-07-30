<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>DHA File Rates | Etihad Admin</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <style>
            .rate-row.sortable-ghost { opacity: 0.45; }
            .rate-row.sortable-chosen { box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.45); }
            .rate-handle { cursor: grab; }
            .rate-handle:active { cursor: grabbing; }
        </style>
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')

            <main class="flex-1 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 transition-colors">
                <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">DHA File Rates</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Page SEO, headings, and all file rates in one place — drag to reorder, edit prices, then Save once.</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('dha-file-rates') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">View page</a>
                    </div>
                </header>

                <section class="px-6 md:px-8 py-6 md:py-8 space-y-6">
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.dha-file-rates.update') }}" class="space-y-6" id="dha-file-rates-form">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">
                                <ul class="list-disc pl-4 space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Page content --}}
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Page content</h2>
                                <label class="inline-flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="is_published" value="1" class="rounded border-slate-400" {{ old('is_published', $setting->is_published) ? 'checked' : '' }} />
                                    Published (visible on portal)
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="heading" class="block text-slate-700 dark:text-slate-300 text-sm">Heading</label>
                                    <input id="heading" name="heading" type="text" required value="{{ old('heading', $setting->heading) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="subheading" class="block text-slate-700 dark:text-slate-300 text-sm">Subheading</label>
                                    <input id="subheading" name="subheading" type="text" value="{{ old('subheading', $setting->subheading) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label for="details" class="block text-slate-700 dark:text-slate-300 text-sm">Details / intro</label>
                                <textarea id="details" name="details" rows="3" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('details', $setting->details) }}</textarea>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Website Call &amp; WhatsApp buttons use the numbers from <strong>Contact settings</strong>.</p>
                        </div>

                        {{-- SEO --}}
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">SEO</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5 md:col-span-2">
                                    <label for="meta_title" class="block text-slate-700 dark:text-slate-300 text-sm">Meta title</label>
                                    <input id="meta_title" name="meta_title" type="text" value="{{ old('meta_title', $setting->meta_title) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5 md:col-span-2">
                                    <label for="meta_description" class="block text-slate-700 dark:text-slate-300 text-sm">Meta description</label>
                                    <textarea id="meta_description" name="meta_description" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('meta_description', $setting->meta_description) }}</textarea>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="meta_keywords" class="block text-slate-700 dark:text-slate-300 text-sm">Meta keywords</label>
                                    <input id="meta_keywords" name="meta_keywords" type="text" value="{{ old('meta_keywords', $setting->meta_keywords) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="Comma separated" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="canonical_url" class="block text-slate-700 dark:text-slate-300 text-sm">Canonical URL</label>
                                    <input id="canonical_url" name="canonical_url" type="text" value="{{ old('canonical_url', $setting->canonical_url) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="Optional" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="meta_robots" class="block text-slate-700 dark:text-slate-300 text-sm">Robots</label>
                                    <input id="meta_robots" name="meta_robots" type="text" value="{{ old('meta_robots', $setting->meta_robots) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="index, follow" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="twitter_card" class="block text-slate-700 dark:text-slate-300 text-sm">Twitter card</label>
                                    <input id="twitter_card" name="twitter_card" type="text" value="{{ old('twitter_card', $setting->twitter_card) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="summary_large_image" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="og_title" class="block text-slate-700 dark:text-slate-300 text-sm">Open Graph title</label>
                                    <input id="og_title" name="og_title" type="text" value="{{ old('og_title', $setting->og_title) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="og_description" class="block text-slate-700 dark:text-slate-300 text-sm">Open Graph description</label>
                                    <input id="og_description" name="og_description" type="text" value="{{ old('og_description', $setting->og_description) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                            </div>
                        </div>

                        {{-- Rates listing --}}
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <div class="flex items-start justify-between gap-3 flex-wrap">
                                <div>
                                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">File rates listing</h2>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Drag to reorder. <strong>File number</strong> is admin-only (not shown on website). Call/WhatsApp on the site use Contact settings.</p>
                                </div>
                                <button type="button" id="add-rate-row" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-600 text-white hover:bg-emerald-500 transition">+ Add rate</button>
                            </div>

                            <datalist id="dha-file-type-suggestions">
                                @foreach(($typeSuggestions ?? []) as $suggestion)
                                    <option value="{{ $suggestion }}"></option>
                                @endforeach
                            </datalist>

                            <div class="overflow-x-auto -mx-1">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                            <th class="py-2 px-1 w-8"></th>
                                            <th class="py-2 px-2">File no.</th>
                                            <th class="py-2 px-2">DHA phase</th>
                                            <th class="py-2 px-2">Size</th>
                                            <th class="py-2 px-2">Category</th>
                                            <th class="py-2 px-2">Type</th>
                                            <th class="py-2 px-2">Price</th>
                                            <th class="py-2 px-2">Full amount</th>
                                            <th class="py-2 px-2 text-center">Active</th>
                                            <th class="py-2 px-2 w-16"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="rates-list">
                                        @php
                                            $oldRates = old('rates');
                                            if (! is_array($oldRates)) {
                                                $oldRates = $rates->map(fn ($r) => [
                                                    'id' => $r->id,
                                                    'file_number' => $r->file_number,
                                                    'plot_size' => $r->plot_size,
                                                    'category' => $r->category,
                                                    'file_type' => $r->file_type,
                                                    'dha_phase_id' => $r->dha_phase_id,
                                                    'price' => $r->price,
                                                    'price_digits' => $r->price_digits,
                                                    'is_active' => $r->is_active,
                                                ])->all();
                                            }
                                        @endphp
                                        @forelse ($oldRates as $index => $rate)
                                            @include('admin.dha_file_rates._rate_row', [
                                                'index' => $index,
                                                'rate' => $rate,
                                                'dhaPhases' => $dhaPhases,
                                                'categoryOptions' => $categoryOptions,
                                            ])
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <p id="rates-empty" class="text-xs text-slate-500 dark:text-slate-400 {{ count($oldRates) ? 'hidden' : '' }}">No rates yet. Click “Add rate” to create the first row.</p>
                        </div>

                        <div class="sticky bottom-0 z-10 -mx-2 px-2 py-3 bg-gradient-to-t from-slate-100 via-slate-100/95 to-transparent dark:from-slate-950 dark:via-slate-950/95">
                            <div class="flex items-center justify-end gap-3">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-500 shadow-lg shadow-emerald-600/20 transition">
                                    Save all
                                </button>
                            </div>
                        </div>
                    </form>
                </section>
            </main>
        </div>

        <template id="rate-row-template">
            @include('admin.dha_file_rates._rate_row', [
                'index' => '__INDEX__',
                'rate' => [
                    'id' => null,
                    'file_number' => '',
                    'plot_size' => '',
                    'category' => 'residential',
                    'file_type' => '',
                    'dha_phase_id' => '',
                    'price' => '',
                    'price_digits' => '',
                    'is_active' => true,
                ],
                'dhaPhases' => $dhaPhases,
                'categoryOptions' => $categoryOptions,
            ])
        </template>

        <script>
            (function () {
                var list = document.getElementById('rates-list');
                var emptyHint = document.getElementById('rates-empty');
                var addBtn = document.getElementById('add-rate-row');
                var tpl = document.getElementById('rate-row-template');

                function reindex() {
                    var rows = list.querySelectorAll('.rate-row');
                    rows.forEach(function (row, i) {
                        row.querySelectorAll('[name]').forEach(function (el) {
                            el.name = el.name.replace(/rates\[(?:\d+|__INDEX__)\]/, 'rates[' + i + ']');
                        });
                    });
                    if (emptyHint) emptyHint.classList.toggle('hidden', rows.length > 0);
                }

                if (window.Sortable && list) {
                    Sortable.create(list, {
                        handle: '.rate-handle',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        onEnd: reindex
                    });
                }

                if (addBtn && tpl && list) {
                    addBtn.addEventListener('click', function () {
                        var html = tpl.innerHTML.replace(/__INDEX__/g, String(list.querySelectorAll('.rate-row').length));
                        list.insertAdjacentHTML('beforeend', html);
                        reindex();
                    });
                }

                list.addEventListener('click', function (e) {
                    var btn = e.target.closest('.remove-rate-row');
                    if (!btn) return;
                    var row = btn.closest('.rate-row');
                    if (row) row.remove();
                    reindex();
                });
            })();
        </script>
    </body>
</html>
