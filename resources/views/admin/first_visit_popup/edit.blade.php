<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>First visit popup | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">First visit popup</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Edit the first-time visitor popup content, background, and CTA form.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.site-analytics.index') }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">View analytics</a>
                        @include('admin.partials.theme-toggle')
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8 space-y-6">
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg max-w-4xl">
                        <form method="POST" action="{{ route('admin.first-visit-popup.update') }}" enctype="multipart/form-data" class="space-y-6 text-sm">
                            @csrf
                            @method('PUT')
                            @if ($errors->any())
                                <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                            @endif

                            <div class="flex flex-wrap gap-6">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_enabled" value="1" class="rounded border-slate-400" {{ old('is_enabled', $popup->is_enabled) ? 'checked' : '' }} />
                                    <span>Enable popup for first-time visitors</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="show_logo" value="1" class="rounded border-slate-400" {{ old('show_logo', $popup->show_logo) ? 'checked' : '' }} />
                                    <span>Show Etihad logo</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="force_show_every_time" value="1" class="rounded border-slate-400" {{ old('force_show_every_time', $popup->force_show_every_time) ? 'checked' : '' }} />
                                    <span>Show every time <span class="text-amber-600 dark:text-amber-400">(testing mode — turn off for production)</span></span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5 sm:col-span-2">
                                    <label class="block">Eyebrow / top label</label>
                                    <input name="eyebrow" value="{{ old('eyebrow', $popup->eyebrow) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" />
                                </div>
                                <div class="space-y-1.5 sm:col-span-2">
                                    <label class="block">Heading</label>
                                    <input name="heading" value="{{ old('heading', $popup->heading) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" />
                                </div>
                                <div class="space-y-1.5 sm:col-span-2">
                                    <label class="block">Subheading</label>
                                    <input name="subheading" value="{{ old('subheading', $popup->subheading) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" />
                                </div>
                                <div class="space-y-1.5 sm:col-span-2">
                                    <label class="block">Body text</label>
                                    <textarea name="body_text" rows="3" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5">{{ old('body_text', $popup->body_text) }}</textarea>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block">CTA button label</label>
                                    <input name="cta_label" value="{{ old('cta_label', $popup->cta_label) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block">Show delay (ms)</label>
                                    <input type="number" min="0" max="5000" name="delay_ms" value="{{ old('delay_ms', $popup->delay_ms) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" />
                                    <p class="text-[11px] text-slate-500">0 = show as soon as possible (recommended).</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block">Form heading (flip side)</label>
                                    <input name="form_heading" value="{{ old('form_heading', $popup->form_heading) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block">Form submit label</label>
                                    <input name="form_submit_label" value="{{ old('form_submit_label', $popup->form_submit_label) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" />
                                </div>
                            </div>

                            <div>
                                <h2 class="text-sm font-semibold mb-2">Background image (optional)</h2>
                                <p class="text-xs text-slate-500 mb-3">If empty, the navy/gold agent-style panel is used.</p>
                                @if($popup->background_image)
                                    <div class="mb-2 flex items-center gap-3 flex-wrap">
                                        <img src="{{ asset('storage/' . $popup->background_image) }}" alt="" class="max-h-24 rounded-lg border border-slate-300 dark:border-slate-700 object-cover" />
                                        <label class="text-xs text-rose-600 dark:text-rose-400 cursor-pointer">
                                            <input type="checkbox" name="remove_background_image" value="1" class="rounded border-slate-400" /> Remove current image
                                        </label>
                                    </div>
                                @endif
                                <input type="file" name="background_image" accept="image/*" class="block w-full text-sm" />
                            </div>

                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save popup settings</button>
                        </form>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
