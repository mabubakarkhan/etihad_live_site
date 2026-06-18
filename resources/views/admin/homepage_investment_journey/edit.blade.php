<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Investment Journey | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Investment Journey</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Timeline section on the root homepage — heading and process steps.</p>
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

                    <form method="POST" action="{{ route('admin.homepage-investment-journey.update') }}" class="space-y-6 max-w-3xl" id="journey-form">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Section heading</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Displayed as white text + teal highlight span. Step cards display at ~<strong>340×95 px</strong> on desktop; titles are uppercased by CSS.</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="title_line_1" class="block text-slate-700 dark:text-slate-300">Title (main)</label>
                                    <input id="title_line_1" name="title_line_1" type="text" value="{{ old('title_line_1', $setting->title_line_1) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="title_highlight" class="block text-slate-700 dark:text-slate-300">Title (highlight)</label>
                                    <input id="title_highlight" name="title_highlight" type="text" value="{{ old('title_highlight', $setting->title_highlight) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-amber-500/30 bg-amber-500/5 dark:bg-amber-500/10 p-4 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong class="text-slate-800 dark:text-slate-100">Timeline steps</strong> — Up to 8 steps. Layout alternates left/right on desktop (best with 4–5 steps). Include the step number in the title, e.g. <em>1. Discovery</em>.
                        </div>

                        <div id="steps-list" class="space-y-4">
                            @php $oldSteps = old('steps', $steps->map(fn ($s) => ['id' => $s->id, 'title' => $s->title, 'description' => $s->description])->all()); @endphp
                            @foreach ($oldSteps as $index => $step)
                                <div class="step-row rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Step <span class="step-number">{{ $index + 1 }}</span></h2>
                                        <button type="button" class="remove-step text-xs text-rose-600 dark:text-rose-400 hover:underline" @if(count($oldSteps) <= 1) hidden @endif>Remove</button>
                                    </div>
                                    @if(!empty($step['id']))
                                        <input type="hidden" name="steps[{{ $index }}][id]" value="{{ $step['id'] }}" />
                                    @endif
                                    <div class="space-y-1.5">
                                        <label class="block text-slate-700 dark:text-slate-300">Step title</label>
                                        <input name="steps[{{ $index }}][title]" type="text" value="{{ $step['title'] ?? '' }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-slate-700 dark:text-slate-300">Description</label>
                                        <textarea name="steps[{{ $index }}][description]" rows="3" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ $step['description'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="add-step" class="inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-700 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">+ Add step</button>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save investment journey</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>

        <template id="step-template">
            <div class="step-row rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Step <span class="step-number"></span></h2>
                    <button type="button" class="remove-step text-xs text-rose-600 dark:text-rose-400 hover:underline">Remove</button>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-slate-700 dark:text-slate-300">Step title</label>
                    <input name="" type="text" required class="step-title block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                </div>
                <div class="space-y-1.5">
                    <label class="block text-slate-700 dark:text-slate-300">Description</label>
                    <textarea name="" rows="3" required class="step-description block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm"></textarea>
                </div>
            </div>
        </template>

        <script>
            (function () {
                const list = document.getElementById('steps-list');
                const template = document.getElementById('step-template');
                const addBtn = document.getElementById('add-step');
                const maxSteps = 8;

                function reindex() {
                    const rows = list.querySelectorAll('.step-row');
                    rows.forEach((row, index) => {
                        row.querySelector('.step-number').textContent = index + 1;
                        const title = row.querySelector('.step-title') || row.querySelector('input[name*="[title]"]');
                        const description = row.querySelector('.step-description') || row.querySelector('textarea[name*="[description]"]');
                        if (title) title.name = `steps[${index}][title]`;
                        if (description) description.name = `steps[${index}][description]`;
                        const idInput = row.querySelector('input[name*="[id]"]');
                        if (idInput) idInput.name = `steps[${index}][id]`;
                        const removeBtn = row.querySelector('.remove-step');
                        if (removeBtn) removeBtn.hidden = rows.length <= 1;
                    });
                    addBtn.disabled = rows.length >= maxSteps;
                    addBtn.classList.toggle('opacity-50', rows.length >= maxSteps);
                }

                addBtn.addEventListener('click', function () {
                    if (list.querySelectorAll('.step-row').length >= maxSteps) return;
                    const clone = template.content.cloneNode(true);
                    list.appendChild(clone);
                    reindex();
                });

                list.addEventListener('click', function (event) {
                    if (!event.target.classList.contains('remove-step')) return;
                    const rows = list.querySelectorAll('.step-row');
                    if (rows.length <= 1) return;
                    event.target.closest('.step-row').remove();
                    reindex();
                });

                reindex();
            })();
        </script>
    </body>
</html>
