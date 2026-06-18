<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>What Set Us Apart | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">What Set Us Apart</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Feature cards grid on the root homepage — heading, subtitle, icons, titles, and descriptions.</p>
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

                    <form method="POST" action="{{ route('admin.homepage-what-sets-apart.update') }}" class="space-y-6 max-w-3xl" id="homepage-form" data-homepage-form data-upload-url="{{ route('admin.homepage-media.upload') }}">
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
                                    <label for="title_highlight" class="block text-slate-700 dark:text-slate-300">Title (gold highlight)</label>
                                    <input id="title_highlight" name="title_highlight" type="text" value="{{ old('title_highlight', $setting->title_highlight) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label for="subtitle" class="block text-slate-700 dark:text-slate-300">Subtitle</label>
                                <textarea id="subtitle" name="subtitle" rows="3" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('subtitle', $setting->subtitle) }}</textarea>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-amber-500/30 bg-amber-500/5 dark:bg-amber-500/10 p-4 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong class="text-slate-800 dark:text-slate-100">Feature cards</strong> — 3×2 grid on desktop (~<strong>310×180 px</strong> per card). Icons display at ~<strong>96×96 px</strong> (6rem). Use inline <code class="text-[11px]">&lt;svg&gt;</code> with <code class="text-[11px]">stroke="currentColor"</code> for gold icons, or upload <strong>128×128 px</strong> PNG/SVG. Uploaded image overrides SVG markup.
                        </div>

                        <div id="cards-list" class="space-y-4">
                            @php $oldCards = old('cards', $cards->map(fn ($c) => ['id' => $c->id, 'title' => $c->title, 'description' => $c->description, 'icon_svg' => $c->icon_svg, 'icon_image' => $c->icon_image])->all()); @endphp
                            @foreach ($oldCards as $index => $card)
                                <div class="card-row rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Card <span class="card-number">{{ $index + 1 }}</span></h2>
                                        <button type="button" class="remove-card text-xs text-rose-600 dark:text-rose-400 hover:underline" @if(count($oldCards) <= 1) hidden @endif>Remove</button>
                                    </div>
                                    @if(!empty($card['id']))
                                        <input type="hidden" name="cards[{{ $index }}][id]" value="{{ $card['id'] }}" />
                                    @endif
                                    <div class="space-y-1.5">
                                        <label class="block text-slate-700 dark:text-slate-300">Card title</label>
                                        <input name="cards[{{ $index }}][title]" type="text" value="{{ $card['title'] ?? '' }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-slate-700 dark:text-slate-300">Description</label>
                                        <textarea name="cards[{{ $index }}][description]" rows="3" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ $card['description'] ?? '' }}</textarea>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-slate-700 dark:text-slate-300">Icon SVG markup</label>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Paste a full <code>&lt;svg viewBox="0 0 64 64" …&gt;</code> element. Use <code>stroke="currentColor"</code> so the gold theme color applies.</p>
                                        <textarea name="cards[{{ $index }}][icon_svg]" rows="4" class="icon-svg block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-xs font-mono">{{ $card['icon_svg'] ?? '' }}</textarea>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-slate-700 dark:text-slate-300">Icon image upload (optional)</label>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Recommended: <strong>128×128 px</strong> PNG or SVG — overrides SVG markup above when set.</p>
                                        @include('admin.partials.homepage_media_field', [
                                            'name' => 'icon_image',
                                            'path' => $card['icon_image'] ?? null,
                                            'pathName' => 'cards[' . $index . '][icon_image_path]',
                                            'removeName' => 'cards[' . $index . '][remove_icon_image]',
                                            'accept' => 'image/*,.svg',
                                            'previewClass' => 'h-16 w-16 object-contain rounded border border-slate-200 dark:border-slate-600 bg-slate-900/40 p-2',
                                        ])
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="add-card" class="inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">+ Add card</button>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save section</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>

        <template id="card-template">
            <div class="card-row rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Card <span class="card-number"></span></h2>
                    <button type="button" class="remove-card text-xs text-rose-600 dark:text-rose-400 hover:underline">Remove</button>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-slate-700 dark:text-slate-300">Card title</label>
                    <input type="text" required class="card-title block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                </div>
                <div class="space-y-1.5">
                    <label class="block text-slate-700 dark:text-slate-300">Description</label>
                    <textarea rows="3" required class="card-description block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm"></textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-slate-700 dark:text-slate-300">Icon SVG markup</label>
                    <textarea rows="4" class="icon-svg block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-xs font-mono"></textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-slate-700 dark:text-slate-300">Icon image upload (optional)</label>
                    <div data-homepage-media-wrap data-remove-name="cards[0][remove_icon_image]" data-preview-class="h-16 w-16 object-contain rounded border border-slate-200 dark:border-slate-600 bg-slate-900/40 p-2">
                        <input type="hidden" name="cards[0][icon_image_path]" value="" />
                        <div data-homepage-media-preview class="hidden mb-3"></div>
                        <input type="file" accept="image/*,.svg" class="homepage-media-upload card-icon-image block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5" data-upload-type="icon_image" data-path-name="cards[0][icon_image_path]" data-media-kind="image" />
                        <p data-homepage-media-status class="mt-2 text-xs text-slate-500 dark:text-slate-400"></p>
                    </div>
                </div>
            </div>
        </template>

        <script>
            (function () {
                const list = document.getElementById('cards-list');
                const template = document.getElementById('card-template');
                const addBtn = document.getElementById('add-card');
                const maxCards = 12;

                function reindex() {
                    const rows = list.querySelectorAll('.card-row');
                    rows.forEach((row, index) => {
                        row.querySelector('.card-number').textContent = index + 1;
                        const map = [
                            ['.card-title', 'title'],
                            ['.card-description', 'description'],
                            ['.icon-svg', 'icon_svg'],
                        ];
                        map.forEach(([selector, key]) => {
                            const el = row.querySelector(selector) || row.querySelector(`[name*="[${key}]"]`);
                            if (el) el.name = `cards[${index}][${key}]`;
                        });
                        const pathInput = row.querySelector('input[name*="[icon_image_path]"]');
                        if (pathInput) pathInput.name = `cards[${index}][icon_image_path]`;
                        const uploadInput = row.querySelector('.homepage-media-upload');
                        if (uploadInput) uploadInput.setAttribute('data-path-name', `cards[${index}][icon_image_path]`);
                        const mediaWrap = row.querySelector('[data-homepage-media-wrap]');
                        if (mediaWrap) mediaWrap.setAttribute('data-remove-name', `cards[${index}][remove_icon_image]`);
                        const idInput = row.querySelector('input[name*="[id]"]');
                        if (idInput) idInput.name = `cards[${index}][id]`;
                        const removeIcon = row.querySelector('input[name*="[remove_icon_image]"]');
                        if (removeIcon) removeIcon.name = `cards[${index}][remove_icon_image]`;
                        const removeBtn = row.querySelector('.remove-card');
                        if (removeBtn) removeBtn.hidden = rows.length <= 1;
                    });
                    addBtn.disabled = rows.length >= maxCards;
                    addBtn.classList.toggle('opacity-50', rows.length >= maxCards);
                }

                addBtn.addEventListener('click', function () {
                    if (list.querySelectorAll('.card-row').length >= maxCards) return;
                    list.appendChild(template.content.cloneNode(true));
                    reindex();
                });

                list.addEventListener('click', function (event) {
                    if (!event.target.classList.contains('remove-card')) return;
                    if (list.querySelectorAll('.card-row').length <= 1) return;
                    event.target.closest('.card-row').remove();
                    reindex();
                });

                reindex();
            })();
        </script>
        <script src="{{ asset('theme/js/admin-homepage-media.js') }}"></script>
    </body>
</html>
