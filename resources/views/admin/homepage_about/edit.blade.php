<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>About Etihad | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">About Etihad</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Hero “About” screen (desktop) and mobile About block — text, video, and images.</p>
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

                    <form method="POST" action="{{ route('admin.homepage-about.update') }}" class="space-y-6 max-w-3xl" id="homepage-form" data-homepage-form data-upload-url="{{ route('admin.homepage-media.upload') }}">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Headings</h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="tagline_about" class="block text-slate-700 dark:text-slate-300">Tagline (about)</label>
                                    <input id="tagline_about" name="tagline_about" type="text" value="{{ old('tagline_about', $setting->tagline_about) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="tagline_vision" class="block text-slate-700 dark:text-slate-300">Tagline (vision)</label>
                                    <input id="tagline_vision" name="tagline_vision" type="text" value="{{ old('tagline_vision', $setting->tagline_vision) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
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
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Showcase video</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Bottom-left on desktop About screen; below content on mobile. Recommended production size: <strong>1280×720 px</strong> or <strong>1920×1080 px</strong> (16:9) — displays at <strong>~544×314 px</strong> (<code class="text-[11px]">object-fit: cover</code>). MP4 or WebM, max 100&nbsp;MB.</p>

                            @include('admin.partials.homepage_media_field', [
                                'name' => 'video',
                                'path' => $setting->video,
                                'kind' => 'video',
                                'accept' => 'video/mp4,video/webm',
                            ])
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Video captions</h2>

                            <div class="space-y-1.5">
                                <label for="media_caption_1" class="block text-slate-700 dark:text-slate-300">Caption 1 (primary)</label>
                                <textarea id="media_caption_1" name="media_caption_1" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('media_caption_1', $setting->media_caption_1) }}</textarea>
                            </div>

                            <div class="space-y-1.5">
                                <label for="media_caption_2" class="block text-slate-700 dark:text-slate-300">Caption 2 (desktop scroll state, optional)</label>
                                <textarea id="media_caption_2" name="media_caption_2" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('media_caption_2', $setting->media_caption_2) }}</textarea>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Center image</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Main portrait photo (center desktop / top mobile). Recommended production size: <strong>600×900 px</strong> (2:3 portrait) — displays at <strong>~475×720 px</strong> on mobile (<code class="text-[11px]">object-fit: cover</code>).</p>

                            @include('admin.partials.homepage_media_field', [
                                'name' => 'center_image',
                                'path' => $setting->center_image,
                            ])
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Secondary image</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Scroll / vision-state image (desktop center stack + mobile bottom). Recommended production size: <strong>345×440 px</strong> (portrait) — displays at <strong>~345×400 px</strong> (<code class="text-[11px]">object-fit: cover</code>).</p>

                            @include('admin.partials.homepage_media_field', [
                                'name' => 'secondary_image',
                                'path' => $setting->secondary_image,
                            ])
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">About copy (right column / mobile)</h2>

                            <div class="space-y-1.5">
                                <label for="about_para_1_lead" class="block text-slate-700 dark:text-slate-300">Paragraph 1 — opening</label>
                                <textarea id="about_para_1_lead" name="about_para_1_lead" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('about_para_1_lead', $setting->about_para_1_lead) }}</textarea>
                            </div>
                            <div class="space-y-1.5">
                                <label for="about_para_1_highlight" class="block text-slate-700 dark:text-slate-300">Paragraph 1 — highlighted span</label>
                                <textarea id="about_para_1_highlight" name="about_para_1_highlight" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('about_para_1_highlight', $setting->about_para_1_highlight) }}</textarea>
                            </div>
                            <div class="space-y-1.5">
                                <label for="about_para_2_lead" class="block text-slate-700 dark:text-slate-300">Paragraph 2 — opening</label>
                                <textarea id="about_para_2_lead" name="about_para_2_lead" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('about_para_2_lead', $setting->about_para_2_lead) }}</textarea>
                            </div>
                            <div class="space-y-1.5">
                                <label for="about_para_2_highlight" class="block text-slate-700 dark:text-slate-300">Paragraph 2 — highlighted span</label>
                                <textarea id="about_para_2_highlight" name="about_para_2_highlight" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('about_para_2_highlight', $setting->about_para_2_highlight) }}</textarea>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Vision copy (scroll state)</h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="vision_para_1_highlight" class="block text-slate-700 dark:text-slate-300">Paragraph 1 highlight</label>
                                    <input id="vision_para_1_highlight" name="vision_para_1_highlight" type="text" value="{{ old('vision_para_1_highlight', $setting->vision_para_1_highlight) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5 sm:col-span-2">
                                    <label for="vision_para_1_body" class="block text-slate-700 dark:text-slate-300">Paragraph 1 body</label>
                                    <textarea id="vision_para_1_body" name="vision_para_1_body" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('vision_para_1_body', $setting->vision_para_1_body) }}</textarea>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="vision_para_2_lead" class="block text-slate-700 dark:text-slate-300">Paragraph 2 opening</label>
                                    <input id="vision_para_2_lead" name="vision_para_2_lead" type="text" value="{{ old('vision_para_2_lead', $setting->vision_para_2_lead) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="vision_para_2_highlight" class="block text-slate-700 dark:text-slate-300">Paragraph 2 highlight</label>
                                    <input id="vision_para_2_highlight" name="vision_para_2_highlight" type="text" value="{{ old('vision_para_2_highlight', $setting->vision_para_2_highlight) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5 sm:col-span-2">
                                    <label for="vision_para_2_body" class="block text-slate-700 dark:text-slate-300">Paragraph 2 closing</label>
                                    <textarea id="vision_para_2_body" name="vision_para_2_body" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('vision_para_2_body', $setting->vision_para_2_body) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors space-y-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Buttons &amp; links</h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="cta_text" class="block text-slate-700 dark:text-slate-300">CTA button text</label>
                                    <input id="cta_text" name="cta_text" type="text" value="{{ old('cta_text', $setting->cta_text) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="cta_url" class="block text-slate-700 dark:text-slate-300">CTA button URL</label>
                                    <input id="cta_url" name="cta_url" type="text" value="{{ old('cta_url', $setting->cta_url) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="javascript:void(0);" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="affiliated_text" class="block text-slate-700 dark:text-slate-300">Affiliated button text</label>
                                    <input id="affiliated_text" name="affiliated_text" type="text" value="{{ old('affiliated_text', $setting->affiliated_text) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="Affiliated pages" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="affiliated_url" class="block text-slate-700 dark:text-slate-300">Affiliated link URL</label>
                                    <input id="affiliated_url" name="affiliated_url" type="text" value="{{ old('affiliated_url', $setting->affiliated_url) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="https://..." />
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save About Etihad section</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
        <script src="{{ asset('theme/js/admin-homepage-media.js') }}"></script>
    </body>
</html>
