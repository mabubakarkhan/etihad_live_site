<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Make Your Choice | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Make Your Choice</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Full-width stats swiper over a background image on the root homepage.</p>
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

                    <form method="POST" action="{{ route('admin.homepage-choice.update') }}" class="space-y-6 max-w-3xl" id="homepage-form" data-homepage-form data-upload-url="{{ route('admin.homepage-media.upload') }}">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Background images</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">If empty, the homepage uses the bundled static default (<code class="text-[11px]">choice-background-CtCIvw6A.avif</code>). Landscape recommended: <strong>1920×1080 px</strong>. Portrait optional: <strong>1080×1920 px</strong> (falls back to landscape upload, then static default).</p>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-slate-700 dark:text-slate-300 text-sm mb-1.5">Landscape background</label>
                                    @include('admin.partials.homepage_media_field', [
                                        'name' => 'background_image',
                                        'path' => $setting->background_image,
                                        'previewClass' => 'max-h-40 w-full rounded-lg border border-slate-200 dark:border-slate-600 object-cover',
                                    ])
                                </div>
                                <div>
                                    <label class="block text-slate-700 dark:text-slate-300 text-sm mb-1.5">Portrait background (optional)</label>
                                    @include('admin.partials.homepage_media_field', [
                                        'name' => 'background_image_portrait',
                                        'path' => $setting->background_image_portrait,
                                        'previewClass' => 'max-h-40 w-full rounded-lg border border-slate-200 dark:border-slate-600 object-cover',
                                    ])
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Section text</h2>
                            <div class="space-y-1.5">
                                <label for="section_heading" class="block text-slate-700 dark:text-slate-300">Center heading</label>
                                <input id="section_heading" name="section_heading" type="text" value="{{ old('section_heading', $setting->section_heading) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="scroll_label_desktop" class="block text-slate-700 dark:text-slate-300">Scroll label (desktop)</label>
                                    <input id="scroll_label_desktop" name="scroll_label_desktop" type="text" value="{{ old('scroll_label_desktop', $setting->scroll_label_desktop) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="scroll_label_mobile" class="block text-slate-700 dark:text-slate-300">Scroll label (mobile)</label>
                                    <input id="scroll_label_mobile" name="scroll_label_mobile" type="text" value="{{ old('scroll_label_mobile', $setting->scroll_label_mobile) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-amber-500/30 bg-amber-500/5 dark:bg-amber-500/10 p-4 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong class="text-slate-800 dark:text-slate-100">Slides</strong> — Up to 6 panels. <code class="text-[11px]">Counter to</code> drives the count-up animation; <code class="text-[11px]">Counter text</code> is what displays (e.g. <em>10+ </em>). Each panel can have an optional background image with a dark gradient overlay; if empty, the default charcoal panel color is used. Recommended card image: <strong>800×1050 px</strong> (portrait, ~3:4 ratio). JPG, PNG, WebP, or AVIF.
                        </div>

                        <div id="slides-list" class="space-y-4">
                            @php $oldSlides = old('slides', $slides->map(fn ($s) => ['id' => $s->id, 'heading_text' => $s->heading_text, 'counter_to' => $s->counter_to, 'counter_text' => $s->counter_text, 'description' => $s->description, 'card_image' => $s->card_image])->all()); @endphp
                            @foreach ($oldSlides as $index => $slide)
                                <div class="slide-row rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Slide <span class="slide-number">{{ $index + 1 }}</span></h2>
                                        <button type="button" class="remove-slide text-xs text-rose-600 dark:text-rose-400 hover:underline" @if(count($oldSlides) <= 1) hidden @endif>Remove</button>
                                    </div>
                                    @if(!empty($slide['id']))
                                        <input type="hidden" name="slides[{{ $index }}][id]" value="{{ $slide['id'] }}" />
                                    @endif
                                    <div class="space-y-1.5">
                                        <label class="block text-slate-700 dark:text-slate-300">Panel label</label>
                                        <input name="slides[{{ $index }}][heading_text]" type="text" value="{{ $slide['heading_text'] ?? '' }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div class="space-y-1.5">
                                            <label class="block text-slate-700 dark:text-slate-300">Counter to (animation)</label>
                                            <input name="slides[{{ $index }}][counter_to]" type="number" min="0" value="{{ $slide['counter_to'] ?? 0 }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                        </div>
                                        <div class="space-y-1.5 sm:col-span-2">
                                            <label class="block text-slate-700 dark:text-slate-300">Counter text</label>
                                            <input name="slides[{{ $index }}][counter_text]" type="text" value="{{ $slide['counter_text'] ?? '' }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="10+ " />
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-slate-700 dark:text-slate-300">Description (follows counter)</label>
                                        <input name="slides[{{ $index }}][description]" type="text" value="{{ $slide['description'] ?? '' }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="successful projects" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-slate-700 dark:text-slate-300">Panel background image (optional)</label>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Recommended: <strong>800×1050 px</strong> portrait — dark gradient overlay is applied automatically on the homepage.</p>
                                        @include('admin.partials.homepage_media_field', [
                                            'name' => 'card_image',
                                            'path' => $slide['card_image'] ?? null,
                                            'pathName' => 'slides[' . $index . '][card_image_path]',
                                            'removeName' => 'slides[' . $index . '][remove_card_image]',
                                            'previewClass' => 'max-h-40 w-full max-w-xs rounded-lg border border-slate-200 dark:border-slate-600 object-cover',
                                        ])
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="add-slide" class="inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">+ Add slide</button>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save Make Your Choice section</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>

        <template id="slide-template">
            <div class="slide-row rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Slide <span class="slide-number"></span></h2>
                    <button type="button" class="remove-slide text-xs text-rose-600 dark:text-rose-400 hover:underline">Remove</button>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-slate-700 dark:text-slate-300">Panel label</label>
                    <input type="text" required class="slide-heading block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-slate-700 dark:text-slate-300">Counter to (animation)</label>
                        <input type="number" min="0" required class="slide-counter-to block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                    </div>
                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="block text-slate-700 dark:text-slate-300">Counter text</label>
                        <input type="text" required class="slide-counter-text block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-slate-700 dark:text-slate-300">Description (follows counter)</label>
                    <input type="text" required class="slide-description block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                </div>
                <div class="space-y-1.5">
                    <label class="block text-slate-700 dark:text-slate-300">Panel background image (optional)</label>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Recommended: <strong>800×1050 px</strong> portrait.</p>
                    <div data-homepage-media-wrap data-remove-name="slides[0][remove_card_image]" data-preview-class="max-h-40 w-full max-w-xs rounded-lg border border-slate-200 dark:border-slate-600 object-cover">
                        <input type="hidden" name="slides[0][card_image_path]" value="" />
                        <div data-homepage-media-preview class="hidden mb-3"></div>
                        <input type="file" accept="image/*" class="homepage-media-upload slide-card-image block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" data-upload-type="card_image" data-path-name="slides[0][card_image_path]" data-media-kind="image" />
                        <p data-homepage-media-status class="mt-2 text-xs text-slate-500 dark:text-slate-400"></p>
                    </div>
                </div>
            </div>
        </template>

        <script>
            (function () {
                const list = document.getElementById('slides-list');
                const template = document.getElementById('slide-template');
                const addBtn = document.getElementById('add-slide');
                const maxSlides = 6;

                function reindex() {
                    const rows = list.querySelectorAll('.slide-row');
                    rows.forEach((row, index) => {
                        row.querySelector('.slide-number').textContent = index + 1;
                        const fields = [
                            ['.slide-heading', 'heading_text'],
                            ['.slide-counter-to', 'counter_to'],
                            ['.slide-counter-text', 'counter_text'],
                            ['.slide-description', 'description'],
                        ];
                        fields.forEach(([selector, key]) => {
                            const el = row.querySelector(selector) || row.querySelector(`[name*="[${key}]"]`);
                            if (el) el.name = `slides[${index}][${key}]`;
                        });
                        const idInput = row.querySelector('input[name*="[id]"]');
                        if (idInput) idInput.name = `slides[${index}][id]`;
                        const pathInput = row.querySelector('input[name*="[card_image_path]"]');
                        if (pathInput) pathInput.name = `slides[${index}][card_image_path]`;
                        const uploadInput = row.querySelector('.homepage-media-upload');
                        if (uploadInput) uploadInput.setAttribute('data-path-name', `slides[${index}][card_image_path]`);
                        const mediaWrap = row.querySelector('[data-homepage-media-wrap]');
                        if (mediaWrap) mediaWrap.setAttribute('data-remove-name', `slides[${index}][remove_card_image]`);
                        const removeImage = row.querySelector('input[name*="[remove_card_image]"]');
                        if (removeImage) removeImage.name = `slides[${index}][remove_card_image]`;
                        const removeBtn = row.querySelector('.remove-slide');
                        if (removeBtn) removeBtn.hidden = rows.length <= 1;
                    });
                    addBtn.disabled = rows.length >= maxSlides;
                    addBtn.classList.toggle('opacity-50', rows.length >= maxSlides);
                }

                addBtn.addEventListener('click', function () {
                    if (list.querySelectorAll('.slide-row').length >= maxSlides) return;
                    list.appendChild(template.content.cloneNode(true));
                    reindex();
                });

                list.addEventListener('click', function (event) {
                    if (!event.target.classList.contains('remove-slide')) return;
                    if (list.querySelectorAll('.slide-row').length <= 1) return;
                    event.target.closest('.slide-row').remove();
                    reindex();
                });

                reindex();
            })();
        </script>
        <script src="{{ asset('theme/js/admin-homepage-media.js') }}"></script>
    </body>
</html>
