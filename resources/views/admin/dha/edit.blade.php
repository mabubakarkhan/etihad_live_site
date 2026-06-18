<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DHA Main Page | Etihad Admin</title>
    @include('admin.partials.theme-init')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
<div class="min-h-screen flex">
    @include('admin.partials.sidebar')
    <main class="flex-1 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 transition-colors">
        <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold tracking-tight">DHA Main Page</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    <a href="{{ route('admin.dha-phases.index') }}" class="text-sky-600 dark:text-sky-400 hover:underline">Manage phases</a>
                    · <a href="{{ route('dha.index') }}" target="_blank" rel="noopener" class="text-emerald-600 dark:text-emerald-400 hover:underline">View on site</a>
                </p>
            </div>
            <div class="flex items-center gap-3">@include('admin.partials.theme-toggle')</div>
        </header>
        <section class="px-6 md:px-8 py-6 md:py-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
            @endif
            <form id="dha-form" method="POST" action="{{ route('admin.dha.update') }}" data-upload-url="{{ route('admin.dha.upload-media') }}" class="space-y-6 max-w-4xl">
                @csrf
                @method('PUT')
                @if ($errors->any())
                    <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                @endif
                @php $heroStats = old('hero_stats', $dha->heroStats()); @endphp
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg">
                    <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider mb-1">Hero section</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Full-viewport hero on <code>/dha</code> — background image, title, buttons, and bottom stats bar.</p>
                    <div class="space-y-4">
                        <div class="grid md:grid-cols-3 gap-4">
                            <div><label class="block text-sm mb-1">Eyebrow</label>
                                <input name="hero_eyebrow" type="text" value="{{ old('hero_eyebrow', $dha->hero_eyebrow) }}" placeholder="WELCOME TO" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
                            <div><label class="block text-sm mb-1">Title (gold)</label>
                                <input name="hero_title_gold" type="text" value="{{ old('hero_title_gold', $dha->hero_title_gold) }}" placeholder="DHA" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
                            <div><label class="block text-sm mb-1">Title (white)</label>
                                <input name="hero_title_white" type="text" value="{{ old('hero_title_white', $dha->hero_title_white) }}" placeholder="LAHORE" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
                        </div>
                        <div><label class="block text-sm mb-1">Sub-headline</label>
                            <input name="hero_subtitle" type="text" value="{{ old('hero_subtitle', $dha->hero_subtitle) }}" placeholder="Pakistan's Most Prestigious Residential Community" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
                        <div><label class="block text-sm mb-1">Description</label>
                            <textarea name="hero_description" rows="3" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" placeholder="Short intro paragraph below the title…">{{ old('hero_description', $dha->hero_description) }}</textarea></div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div><label class="block text-sm mb-1">Primary button label</label>
                                <input name="hero_btn_primary_label" type="text" value="{{ old('hero_btn_primary_label', $dha->hero_btn_primary_label) }}" placeholder="EXPLORE PROJECTS" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
                            <div><label class="block text-sm mb-1">Primary button URL</label>
                                <input name="hero_btn_primary_url" type="text" value="{{ old('hero_btn_primary_url', $dha->hero_btn_primary_url) }}" placeholder="#dha-phases" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
                            <div><label class="block text-sm mb-1">Secondary button label</label>
                                <input name="hero_btn_secondary_label" type="text" value="{{ old('hero_btn_secondary_label', $dha->hero_btn_secondary_label) }}" placeholder="VIEW MASTER PLAN" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
                            <div><label class="block text-sm mb-1">Secondary button URL</label>
                                <input name="hero_btn_secondary_url" type="text" value="{{ old('hero_btn_secondary_url', $dha->hero_btn_secondary_url) }}" placeholder="https://… or #" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Stats bar (up to 5 items)</h3>
                            <p class="text-xs text-slate-500">Lucide icon names — e.g. <code>users</code>, <code>map</code>, <code>shield-check</code>, <code>tree-pine</code>, <code>building-2</code></p>
                            @foreach(range(0, 4) as $i)
                                @php $st = $heroStats[$i] ?? ['icon' => '', 'value' => '', 'label' => '']; @endphp
                                <div class="grid md:grid-cols-3 gap-2 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800">
                                    <input name="hero_stats[{{ $i }}][icon]" type="text" value="{{ $st['icon'] ?? '' }}" placeholder="Icon" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                                    <input name="hero_stats[{{ $i }}][value]" type="text" value="{{ $st['value'] ?? '' }}" placeholder="Value (e.g. 54,541+)" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                                    <input name="hero_stats[{{ $i }}][label]" type="text" value="{{ $st['label'] ?? '' }}" placeholder="Label (e.g. Total Plots)" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg">
                    <h2 class="text-sm font-semibold mb-4">Content</h2>
                    <div class="space-y-4">
                        <div><label class="block text-sm mb-1">Title</label>
                            <input name="title" type="text" value="{{ old('title', $dha->title) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" required /></div>
                        <div><label class="block text-sm mb-1">URL slug</label>
                            <input name="slug" type="text" value="{{ old('slug', $dha->slug) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
                        <div><label class="block text-sm mb-1">Heading (fallback if hero title empty)</label>
                            <input name="heading" type="text" value="{{ old('heading', $dha->heading) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
                        <div><label class="block text-sm mb-1">Rich content</label>
                            <div class="richtext-wrap bg-slate-50 dark:bg-slate-950/60 rounded-lg border border-slate-300 dark:border-slate-700 min-h-[200px]">
                                <textarea name="content" id="dha_content" class="hidden">{{ old('content', $dha->content) }}</textarea>
                            </div></div>
                        <div><label class="block text-sm mb-1">Status</label>
                            <select name="status" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm">
                                <option value="active" @selected(old('status', $dha->status) === 'active')>Active</option>
                                <option value="inactive" @selected(old('status', $dha->status) === 'inactive')>Inactive</option>
                            </select></div>
                    </div>
                </div>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg" data-media-wrap>
                    <h2 class="text-sm font-semibold mb-1">Hero background image</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Recommended <strong>1920×1080px</strong> or wider landscape. Full-viewport hero background on <code>/dha</code>. City/skyline works best on the right; left side is covered by the dark gradient.</p>
                    @php
                        $dhaFeaturedPath = old('featured_image_path', '');
                        $dhaExistingFeatured = (!$dhaFeaturedPath && $dha->featured_image) ? $dha->featured_image : '';
                    @endphp
                    <input type="hidden" name="featured_image_path" id="dha-featured-path" value="{{ $dhaFeaturedPath }}" />
                    <div id="dha-featured-existing" class="{{ $dhaExistingFeatured && !$dhaFeaturedPath ? '' : 'hidden' }} mb-2 flex items-center gap-3 flex-wrap">
                        @if($dhaExistingFeatured)
                            <img src="{{ asset('storage/' . $dhaExistingFeatured) }}" alt="" class="dha-featured-preview-img h-20 rounded border border-slate-300 dark:border-slate-700 object-cover" />
                        @endif
                        <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer">
                            <input type="checkbox" name="remove_featured_image" value="1" class="rounded border-slate-400" {{ old('remove_featured_image') ? 'checked' : '' }} /> Remove current image
                        </label>
                    </div>
                    <div id="dha-featured-preview" class="{{ $dhaFeaturedPath ? '' : 'hidden' }} mb-2 flex items-center gap-3 flex-wrap">
                        @if($dhaFeaturedPath)
                            <img src="{{ asset('storage/' . $dhaFeaturedPath) }}" alt="" class="dha-featured-preview-img h-20 rounded border border-slate-300 dark:border-slate-700 object-cover" />
                        @else
                            <img src="" alt="" class="dha-featured-preview-img h-20 rounded border border-slate-300 dark:border-slate-700 object-cover hidden" />
                        @endif
                    </div>
                    <div id="dha-featured-upload-status" class="hidden mb-2 rounded-lg border border-sky-500/40 bg-sky-500/10 px-3 py-2 text-sm text-sky-800 dark:text-sky-200">
                        <span class="inline-block w-3 h-3 border-2 border-sky-600 border-t-transparent rounded-full animate-spin mr-2 align-middle"></span>
                        Uploading featured image, please wait…
                    </div>
                    <div id="dha-featured-upload-success" class="hidden mb-2 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-sm text-emerald-800 dark:text-emerald-200"></div>
                    <div id="dha-featured-upload-error" class="hidden mb-2 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200"></div>
                    <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Featured image — 1920×600px recommended (uploads immediately)</label>
                    <input type="file" accept="image/*" class="dha-media-upload block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" data-upload-type="featured" data-path-name="featured_image_path" data-status-prefix="dha-featured" />
                </div>
                @include('admin.dha._sections_form', ['dha' => $dha])

                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg">
                    <h2 class="text-sm font-semibold mb-4">SEO</h2>
                    <div class="space-y-4">
                        <div><label class="block text-sm mb-1">Meta title</label><input name="meta_title" type="text" value="{{ old('meta_title', $dha->meta_title) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
                        <div><label class="block text-sm mb-1">Meta description</label><textarea name="meta_description" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm">{{ old('meta_description', $dha->meta_description) }}</textarea></div>
                        <div><label class="block text-sm mb-1">Meta keywords</label><input name="meta_keywords" type="text" value="{{ old('meta_keywords', $dha->meta_keywords) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
                        <div><label class="block text-sm mb-1">Canonical URL</label><input name="canonical_url" type="text" value="{{ old('canonical_url', $dha->canonical_url) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
                    </div>
                </div>
                <button type="submit" class="inline-flex rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 hover:bg-emerald-400">Save DHA page</button>
            </form>
        </section>
    </main>
</div>
<script src="{{ asset('theme/js/admin-dha-media.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('dha_content');
    if (!el || typeof Quill === 'undefined') return;
    var q = new Quill(el.parentElement, { theme: 'snow' });
    q.root.innerHTML = el.value;
    el.closest('form').addEventListener('submit', function() { el.value = q.root.innerHTML; });
});
</script>
</body>
</html>
