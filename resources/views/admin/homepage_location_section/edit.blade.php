<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Location Section | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Location Section</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Homepage location block images. Address, office title, map link, and office notes load from <a href="{{ route('admin.contact-settings.edit') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">Contact settings</a>.</p>
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

                    <form method="POST" action="{{ route('admin.homepage-location-section.update') }}" class="space-y-6 max-w-2xl" id="homepage-form" data-homepage-form data-upload-url="{{ route('admin.homepage-media.upload') }}">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif

                        <div class="rounded-2xl border border-amber-500/30 bg-amber-500/5 dark:bg-amber-500/10 p-4 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            Text on the location card (office name, address, visiting note, Take me there link) is managed under <strong>Contact settings</strong>. Upload images here only.
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Map background</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Wide cityscape behind the location card. Recommended: landscape, at least <strong>1920×1080 px</strong>.</p>
                            @include('admin.partials.homepage_media_field', [
                                'name' => 'map_background_image',
                                'path' => $setting->map_background_image,
                                'previewClass' => 'max-h-40 w-full rounded-lg object-cover border border-slate-200 dark:border-slate-700',
                            ])
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Card photo</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Interior/office image at the top of the white location card. Recommended: <strong>800×600 px</strong> or similar landscape.</p>
                            @include('admin.partials.homepage_media_field', [
                                'name' => 'card_image',
                                'path' => $setting->card_image,
                                'previewClass' => 'max-h-40 w-full rounded-lg object-cover border border-slate-200 dark:border-slate-700',
                            ])
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Map pin</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Teal pin overlay on the map background. Recommended: PNG with transparency, about <strong>120×160 px</strong>.</p>
                            @include('admin.partials.homepage_media_field', [
                                'name' => 'pin_image',
                                'path' => $setting->pin_image,
                                'previewClass' => 'h-20 w-auto rounded border border-slate-200 dark:border-slate-700 bg-slate-900/20',
                            ])
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save location images</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
        <script src="{{ asset('theme/js/admin-homepage-media.js') }}"></script>
    </body>
</html>
