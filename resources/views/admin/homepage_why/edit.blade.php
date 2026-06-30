<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Why Choose Etihad | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Why Choose Etihad</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Heading, description, scroll label, and four collage images on the root homepage.</p>
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

                    <form method="POST" action="{{ route('admin.homepage-why.update') }}" class="space-y-6 max-w-3xl" id="homepage-form" data-homepage-form data-upload-url="{{ route('admin.homepage-media.upload') }}">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Section text</h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="heading_line_1" class="block text-slate-700 dark:text-slate-300">Heading (line 1)</label>
                                    <input id="heading_line_1" name="heading_line_1" type="text" value="{{ old('heading_line_1', $setting->heading_line_1) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Shown in white, e.g. <em>WHY CHOOSE</em></p>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="heading_line_2" class="block text-slate-700 dark:text-slate-300">Heading (line 2)</label>
                                    <input id="heading_line_2" name="heading_line_2" type="text" value="{{ old('heading_line_2', $setting->heading_line_2) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Shown in gold, e.g. <em>ETIHAD?</em></p>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label for="description" class="block text-slate-700 dark:text-slate-300">Description</label>
                                <textarea id="description" name="description" rows="5" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('description', $setting->description) }}</textarea>
                            </div>

                            <div class="space-y-1.5">
                                <label for="scroll_label" class="block text-slate-700 dark:text-slate-300">Scroll indicator label</label>
                                <input id="scroll_label" name="scroll_label" type="text" value="{{ old('scroll_label', $setting->scroll_label) }}" class="block w-full max-w-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                            </div>

                            <div class="space-y-1.5">
                                <label for="contemporary_heading" class="block text-slate-700 dark:text-slate-300">Contemporary block heading</label>
                                <input id="contemporary_heading" name="contemporary_heading" type="text" value="{{ old('contemporary_heading', $setting->contemporary_heading ?: 'CONTEMPORARY') }}" class="block w-full max-w-md rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                <p class="text-xs text-slate-500 dark:text-slate-400">Large centered title above the four-image collage. Default: <em>CONTEMPORARY</em></p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-amber-500/30 bg-amber-500/5 dark:bg-amber-500/10 p-4 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong class="text-slate-800 dark:text-slate-100">Image collage</strong> — The four photos appear in the <em>Contemporary</em> block above this section and animate into <em>Why Choose Etihad</em> on scroll. Upload portrait-oriented images at the production sizes below; the layout crops with <code class="text-[11px]">object-fit: cover</code> to the display slots.
                        </div>

                        @php
                            $imageFields = [
                                'image_left' => [
                                    'label' => 'Image 1 — Left',
                                    'production' => '345×440 px',
                                    'display' => '~306×390 px',
                                ],
                                'image_center' => [
                                    'label' => 'Image 2 — Center (main)',
                                    'production' => '470×600 px',
                                    'display' => '~323×413 px',
                                ],
                                'image_right' => [
                                    'label' => 'Image 3 — Right',
                                    'production' => '345×440 px',
                                    'display' => '~345×400 px',
                                ],
                                'image_center_back' => [
                                    'label' => 'Image 4 — Center back',
                                    'production' => '466×594 px',
                                    'display' => '~350×420 px',
                                ],
                            ];
                        @endphp

                        @foreach ($imageFields as $field => $meta)
                            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-3">
                                <div>
                                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $meta['label'] }}</h2>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        Recommended production size: <strong>{{ $meta['production'] }}</strong> —
                                        displays at <strong>{{ $meta['display'] }}</strong> on desktop (cover crop).
                                    </p>
                                </div>

                                @include('admin.partials.homepage_media_field', [
                                    'name' => $field,
                                    'path' => $setting->{$field},
                                    'previewClass' => 'max-h-40 rounded-lg border border-slate-200 dark:border-slate-600 object-cover',
                                ])
                            </div>
                        @endforeach

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save Why Choose section</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
        <script src="{{ asset('theme/js/admin-homepage-media.js') }}"></script>
    </body>
</html>
