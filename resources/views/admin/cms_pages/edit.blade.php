<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Edit {{ $cmsPage->name }} | CMS | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">
                            {{ $cmsPage->name }}
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            <a href="{{ route('admin.cms-pages.index') }}" class="text-sky-600 dark:text-sky-400 hover:underline">CMS Pages</a>
                            · Update content, SEO, and banner.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.cms-pages.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Back to CMS</a>
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-flex">@csrf<button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Logout</button></form>
                    </div>
                </header>

                <section class="px-6 md:px-8 py-6 md:py-8 space-y-6">
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.cms-pages.update', $cmsPage) }}" enctype="multipart/form-data" class="space-y-6 max-w-3xl">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-4">Content</h2>
                            <div class="space-y-4">
                                <div class="space-y-1.5">
                                    <label for="heading" class="block text-slate-700 dark:text-slate-300">Heading</label>
                                    <input id="heading" name="heading" type="text" value="{{ old('heading', $cmsPage->heading ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" placeholder="Page heading" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-700 dark:text-slate-300">Content (rich text)</label>
                                    <div class="richtext-wrap bg-slate-50 dark:bg-slate-950/60 rounded-lg border border-slate-300 dark:border-slate-700 min-h-[180px]">
                                        <textarea name="content" id="cms_content" rows="6" class="richtext hidden">{{ old('content', $cmsPage->content ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-4">Wide banner</h2>
                            @if($cmsPage->banner_image)
                                <div class="mb-3 flex items-center gap-3 flex-wrap">
                                    <img src="{{ asset('storage/' . $cmsPage->banner_image) }}" alt="Banner" class="max-h-24 rounded-lg border border-slate-200 dark:border-slate-600 object-cover" />
                                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer">
                                        <input type="checkbox" name="remove_banner_image" value="1" class="rounded border-slate-400" /> Remove banner
                                    </label>
                                </div>
                            @endif
                            <input type="file" name="banner_image" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" />
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-4">SEO</h2>
                            <div class="space-y-4">
                                <div class="space-y-1.5">
                                    <label for="meta_title" class="block text-slate-700 dark:text-slate-300">Meta title</label>
                                    <input id="meta_title" name="meta_title" type="text" value="{{ old('meta_title', $cmsPage->meta_title ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="meta_description" class="block text-slate-700 dark:text-slate-300">Meta description</label>
                                    <textarea id="meta_description" name="meta_description" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">{{ old('meta_description', $cmsPage->meta_description ?? '') }}</textarea>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="meta_keywords" class="block text-slate-700 dark:text-slate-300">Meta keywords</label>
                                    <input id="meta_keywords" name="meta_keywords" type="text" value="{{ old('meta_keywords', $cmsPage->meta_keywords ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" placeholder="Comma separated" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="canonical_url" class="block text-slate-700 dark:text-slate-300">Canonical URL</label>
                                    <input id="canonical_url" name="canonical_url" type="text" value="{{ old('canonical_url', $cmsPage->canonical_url ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" placeholder="Optional" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="meta_robots" class="block text-slate-700 dark:text-slate-300">Robots</label>
                                    <input id="meta_robots" name="meta_robots" type="text" value="{{ old('meta_robots', $cmsPage->meta_robots ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" placeholder="index, follow" />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-1">Advanced SEO &amp; social sharing</h2>
                            @if($cmsPage->slug === 'home')
                                <p class="text-xs text-emerald-700 dark:text-emerald-300 mb-4">These fields power the root homepage meta tags, Open Graph, Twitter cards, and JSON-LD.</p>
                            @else
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Optional overrides for richer search and social previews.</p>
                            @endif
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label for="og_title" class="block text-slate-700 dark:text-slate-300">Open Graph title</label>
                                        <input id="og_title" name="og_title" type="text" value="{{ old('og_title', $cmsPage->og_title ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="twitter_card" class="block text-slate-700 dark:text-slate-300">Twitter card type</label>
                                        <input id="twitter_card" name="twitter_card" type="text" value="{{ old('twitter_card', $cmsPage->twitter_card ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="summary_large_image" />
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="og_description" class="block text-slate-700 dark:text-slate-300">Open Graph description</label>
                                    <textarea id="og_description" name="og_description" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100">{{ old('og_description', $cmsPage->og_description ?? '') }}</textarea>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label for="twitter_title" class="block text-slate-700 dark:text-slate-300">Twitter title</label>
                                        <input id="twitter_title" name="twitter_title" type="text" value="{{ old('twitter_title', $cmsPage->twitter_title ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="twitter_description" class="block text-slate-700 dark:text-slate-300">Twitter description</label>
                                        <input id="twitter_description" name="twitter_description" type="text" value="{{ old('twitter_description', $cmsPage->twitter_description ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label for="og_image" class="block text-slate-700 dark:text-slate-300">Open Graph image</label>
                                        @if($cmsPage->og_image)
                                            <div class="mb-2 flex items-center gap-3 flex-wrap">
                                                <img src="{{ asset('storage/' . $cmsPage->og_image) }}" alt="" class="max-h-20 rounded-lg border border-slate-200 dark:border-slate-600 object-cover" />
                                                <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer"><input type="checkbox" name="remove_og_image" value="1" class="rounded border-slate-400" /> Remove</label>
                                            </div>
                                        @endif
                                        <input type="file" name="og_image" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="twitter_image" class="block text-slate-700 dark:text-slate-300">Twitter image</label>
                                        @if($cmsPage->twitter_image)
                                            <div class="mb-2 flex items-center gap-3 flex-wrap">
                                                <img src="{{ asset('storage/' . $cmsPage->twitter_image) }}" alt="" class="max-h-20 rounded-lg border border-slate-200 dark:border-slate-600 object-cover" />
                                                <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer"><input type="checkbox" name="remove_twitter_image" value="1" class="rounded border-slate-400" /> Remove</label>
                                            </div>
                                        @endif
                                        <input type="file" name="twitter_image" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5" />
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="structured_data_json" class="block text-slate-700 dark:text-slate-300">Structured data (JSON-LD)</label>
                                    <textarea id="structured_data_json" name="structured_data_json" rows="8" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm font-mono text-slate-900 dark:text-slate-100" placeholder='{"@@context":"https://schema.org","@@type":"RealEstateAgent",...}'>{{ old('structured_data_json', $cmsPage->structured_data_json ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save page</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Quill !== 'undefined') {
                    var wrap = document.querySelector('.richtext-wrap');
                    if (wrap) {
                        var ta = wrap.querySelector('textarea.richtext');
                        if (ta) {
                            var div = document.createElement('div');
                            div.className = 'quill-editor bg-white dark:bg-slate-950';
                            ta.parentNode.insertBefore(div, ta);
                            var q = new Quill(div, { theme: 'snow', modules: { toolbar: [['bold','italic','underline'],['link'],[{list:'ordered'},{list:'bullet'}]] } });
                            q.root.innerHTML = ta.value;
                            q.on('text-change', function() { ta.value = q.root.innerHTML; });
                        }
                    }
                }
                document.querySelector('form').addEventListener('submit', function() {
                    var wrap = document.querySelector('.richtext-wrap');
                    if (wrap) {
                        var ta = wrap.querySelector('textarea.richtext');
                        var qel = wrap.querySelector('.quill-editor');
                        if (ta && qel && typeof Quill !== 'undefined') {
                            var q = Quill.find(qel);
                            if (q) ta.value = q.root.innerHTML;
                        }
                    }
                });
            });
        </script>
    </body>
</html>
