<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Main hero | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Main hero</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">First homepage hero screen — background image, headings, description, CTA, and scroll label.</p>
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

                    <form method="POST" action="{{ route('admin.homepage-hero.update') }}" class="space-y-6 max-w-3xl" id="homepage-form" data-homepage-form data-upload-url="{{ route('admin.homepage-media.upload') }}">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-1">Main hero background</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Used on the homepage first hero screen. Recommended production size: <strong>1920×1080 px</strong> or larger (16:9 landscape) — full viewport background with <code class="text-[11px]">object-fit: cover</code>.</p>

                            @include('admin.partials.homepage_media_field', [
                                'name' => 'hero_image',
                                'path' => $setting->hero_image,
                                'previewClass' => 'max-h-48 w-full rounded-lg border border-slate-200 dark:border-slate-600 object-cover',
                            ])

                            <div class="mt-4 space-y-1.5">
                                <label for="hero_image_alt" class="block text-slate-700 dark:text-slate-300">Background image alt text</label>
                                <input id="hero_image_alt" name="hero_image_alt" type="text" value="{{ old('hero_image_alt', $setting->hero_image_alt) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Headings</h2>

                            <div class="space-y-1.5">
                                <label for="tagline" class="block text-slate-700 dark:text-slate-300">Top tagline</label>
                                <input id="tagline" name="tagline" type="text" value="{{ old('tagline', $setting->tagline) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="heading_line_1" class="block text-slate-700 dark:text-slate-300">Main heading (line 1)</label>
                                    <input id="heading_line_1" name="heading_line_1" type="text" value="{{ old('heading_line_1', $setting->heading_line_1) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="heading_line_2" class="block text-slate-700 dark:text-slate-300">Main heading (line 2)</label>
                                    <input id="heading_line_2" name="heading_line_2" type="text" value="{{ old('heading_line_2', $setting->heading_line_2) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Description &amp; scroll</h2>

                            <div class="space-y-1.5">
                                <label for="description" class="block text-slate-700 dark:text-slate-300">Bottom description</label>
                                <textarea id="description" name="description" rows="4" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('description', $setting->description) }}</textarea>
                            </div>

                            <div class="space-y-1.5">
                                <label for="scroll_text" class="block text-slate-700 dark:text-slate-300">Scroll indicator text</label>
                                <input id="scroll_text" name="scroll_text" type="text" value="{{ old('scroll_text', $setting->scroll_text) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Call to action</h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="cta_text" class="block text-slate-700 dark:text-slate-300">Button text</label>
                                    <input id="cta_text" name="cta_text" type="text" value="{{ old('cta_text', $setting->cta_text) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="cta_url" class="block text-slate-700 dark:text-slate-300">Button link</label>
                                    <input id="cta_url" name="cta_url" type="text" value="{{ old('cta_url', $setting->cta_url) }}" placeholder="/contact-us" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Relative path (e.g. <code class="text-[11px]">/contact-us</code>) or full URL.</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save homepage hero</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
        <script src="{{ asset('theme/js/admin-homepage-media.js') }}"></script>
    </body>
</html>
