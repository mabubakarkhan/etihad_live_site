<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>DHA Section | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">DHA Section</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">First homepage showcase strip — section copy here; cards load from <a href="{{ route('admin.dha-phases.index') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">DHA Phases</a>.</p>
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

                    <form method="POST" action="{{ route('admin.homepage-dha-section.update') }}" class="space-y-6 max-w-3xl">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif

                        <div class="rounded-2xl border border-amber-500/30 bg-amber-500/5 dark:bg-amber-500/10 p-4 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            Phase cards (image, title, description, stats) are pulled automatically from active DHA phases. Edit phases under <strong>DHA → Phases</strong>. Card images use each phase’s <em>card image</em> (fallback: featured image).
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Section copy</h2>

                            <div class="space-y-1.5">
                                <label for="eyebrow" class="block text-slate-700 dark:text-slate-300">Eyebrow label</label>
                                <input id="eyebrow" name="eyebrow" type="text" value="{{ old('eyebrow', $setting->eyebrow) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="title_line_1" class="block text-slate-700 dark:text-slate-300">Heading (line 1)</label>
                                    <input id="title_line_1" name="title_line_1" type="text" value="{{ old('title_line_1', $setting->title_line_1) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="title_highlight" class="block text-slate-700 dark:text-slate-300">Heading (gold highlight)</label>
                                    <input id="title_highlight" name="title_highlight" type="text" value="{{ old('title_highlight', $setting->title_highlight) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label for="description" class="block text-slate-700 dark:text-slate-300">Description</label>
                                <textarea id="description" name="description" rows="4" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('description', $setting->description) }}</textarea>
                            </div>

                            <div class="space-y-1.5">
                                <label for="footer_note" class="block text-slate-700 dark:text-slate-300">Footer note</label>
                                <input id="footer_note" name="footer_note" type="text" value="{{ old('footer_note', $setting->footer_note) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save DHA section</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
    </body>
</html>
