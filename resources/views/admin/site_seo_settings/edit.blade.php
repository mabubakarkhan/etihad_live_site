<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>SEO &amp; tracking | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">SEO &amp; tracking</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Analytics, pixels, verification tags, and custom scripts for the whole site.</p>
                    </div>
                    <div class="flex items-center gap-3">@include('admin.partials.theme-toggle')</div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8 space-y-6">
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg max-w-4xl">
                        <form method="POST" action="{{ route('admin.site-seo-settings.update') }}" enctype="multipart/form-data" class="space-y-6 text-sm">
                            @csrf
                            @method('PUT')
                            @if ($errors->any())
                                <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                            @endif

                            <div>
                                <h2 class="text-sm font-semibold mb-3">Analytics &amp; pixels</h2>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-1.5"><label class="block">Google Analytics 4 ID</label><input name="google_analytics_id" value="{{ old('google_analytics_id', $siteSeo->google_analytics_id) }}" placeholder="G-XXXXXXXX" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" /></div>
                                    <div class="space-y-1.5"><label class="block">Google Tag Manager ID</label><input name="google_tag_manager_id" value="{{ old('google_tag_manager_id', $siteSeo->google_tag_manager_id) }}" placeholder="GTM-XXXX" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" /></div>
                                    <div class="space-y-1.5"><label class="block">Google Ads ID</label><input name="google_ads_id" value="{{ old('google_ads_id', $siteSeo->google_ads_id) }}" placeholder="AW-XXXXXXXX" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" /></div>
                                    <div class="space-y-1.5"><label class="block">Facebook Pixel ID</label><input name="facebook_pixel_id" value="{{ old('facebook_pixel_id', $siteSeo->facebook_pixel_id) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" /></div>
                                    <div class="space-y-1.5"><label class="block">TikTok Pixel ID</label><input name="tiktok_pixel_id" value="{{ old('tiktok_pixel_id', $siteSeo->tiktok_pixel_id) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" /></div>
                                    <div class="space-y-1.5"><label class="block">LinkedIn Partner ID</label><input name="linkedin_partner_id" value="{{ old('linkedin_partner_id', $siteSeo->linkedin_partner_id) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" /></div>
                                    <div class="space-y-1.5"><label class="block">Hotjar Site ID</label><input name="hotjar_id" value="{{ old('hotjar_id', $siteSeo->hotjar_id) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" /></div>
                                </div>
                            </div>

                            <div>
                                <h2 class="text-sm font-semibold mb-3">Site verification</h2>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-1.5"><label class="block">Google Search Console</label><input name="google_site_verification" value="{{ old('google_site_verification', $siteSeo->google_site_verification) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" /></div>
                                    <div class="space-y-1.5"><label class="block">Bing Webmaster</label><input name="bing_site_verification" value="{{ old('bing_site_verification', $siteSeo->bing_site_verification) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" /></div>
                                    <div class="space-y-1.5 sm:col-span-2"><label class="block">Facebook domain verification</label><input name="facebook_domain_verification" value="{{ old('facebook_domain_verification', $siteSeo->facebook_domain_verification) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5" /></div>
                                </div>
                            </div>

                            <div>
                                <h2 class="text-sm font-semibold mb-3">Default social image</h2>
                                @if($siteSeo->default_og_image)
                                    <div class="mb-2 flex items-center gap-3"><img src="{{ asset('storage/' . $siteSeo->default_og_image) }}" alt="" class="max-h-20 rounded-lg border" /><label class="text-xs text-rose-600"><input type="checkbox" name="remove_default_og_image" value="1" /> Remove</label></div>
                                @endif
                                <input type="file" name="default_og_image" accept="image/*" class="block w-full text-sm" />
                            </div>

                            <div>
                                <h2 class="text-sm font-semibold mb-3">Custom code</h2>
                                <div class="space-y-4">
                                    <div class="space-y-1.5"><label class="block">&lt;head&gt; scripts</label><textarea name="custom_head_code" rows="5" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 font-mono text-xs">{{ old('custom_head_code', $siteSeo->custom_head_code) }}</textarea></div>
                                    <div class="space-y-1.5"><label class="block">After &lt;body&gt; opens</label><textarea name="custom_body_open_code" rows="4" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 font-mono text-xs">{{ old('custom_body_open_code', $siteSeo->custom_body_open_code) }}</textarea></div>
                                    <div class="space-y-1.5"><label class="block">Before &lt;/body&gt;</label><textarea name="custom_body_close_code" rows="4" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 font-mono text-xs">{{ old('custom_body_close_code', $siteSeo->custom_body_close_code) }}</textarea></div>
                                </div>
                            </div>

                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save SEO &amp; tracking</button>
                        </form>
                    </div>
                    @php $homeCmsPage = \App\Models\CmsPage::where('slug', 'home')->first(); @endphp
                    @if($homeCmsPage)
                    <p class="text-xs text-slate-500 max-w-4xl">Homepage title, description, Open Graph, Twitter cards, and JSON-LD are managed under <a href="{{ route('admin.cms-pages.edit', $homeCmsPage) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">CMS → Home</a>.</p>
                    @endif
                </section>
            </main>
        </div>
    </body>
</html>
