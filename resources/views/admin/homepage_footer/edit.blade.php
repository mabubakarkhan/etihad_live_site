<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Footer image | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Footer image</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Large image on the right side of the homepage footer. Contact links, socials, and CTA are unchanged.</p>
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

                    <form method="POST" action="{{ route('admin.homepage-footer.update') }}" class="space-y-6 max-w-2xl" id="homepage-form" data-homepage-form data-upload-url="{{ route('admin.homepage-media.upload') }}">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Footer right image</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Recommended: tall portrait or interior photo, at least <strong>600×900 px</strong>. If you remove the upload, the current default footer image is used.</p>

                            @include('admin.partials.homepage_media_field', [
                                'name' => 'footer_image',
                                'path' => $setting->footer_image,
                                'previewClass' => 'max-h-64 w-full rounded-lg border border-slate-200 dark:border-slate-600 object-cover',
                            ])

                            <div class="space-y-1.5">
                                <label for="footer_image_alt" class="block text-slate-700 dark:text-slate-300">Image alt text</label>
                                <input id="footer_image_alt" name="footer_image_alt" type="text" value="{{ old('footer_image_alt', $setting->footer_image_alt) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save footer image</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
        <script src="{{ asset('theme/js/admin-homepage-media.js') }}"></script>
    </body>
</html>
