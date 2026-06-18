<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Add Dealer | Etihad Admin</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        @include('admin.projects._tom_select_dark')
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')
            <main class="flex-1 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 transition-colors">
                <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Add dealer</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Create a new dealer profile.</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.dealers.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Back to dealers</a>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8">
                    <div class="max-w-2xl rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-6 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors">
                        @if ($errors->any())
                            <div class="mb-4 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif
                        <form method="POST" action="{{ route('admin.dealers.store') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="space-y-1.5">
                                <label for="profile_pic" class="block text-sm text-slate-700 dark:text-slate-300">Profile picture</label>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Recommended 400×400px (1:1). Portrait photo shown on the profile circle.</p>
                                <input id="profile_pic" name="profile_pic" type="file" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" />
                            </div>
                            <div class="space-y-1.5">
                                <label for="name" class="block text-sm text-slate-700 dark:text-slate-300">Name *</label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" />
                            </div>
                            <div class="space-y-1.5">
                                <label for="slug" class="block text-sm text-slate-700 dark:text-slate-300">Slug</label>
                                <input id="slug" name="slug" type="text" value="{{ old('slug') }}" placeholder="url-friendly-id" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" />
                                <p class="text-xs text-slate-500 dark:text-slate-400">Leave empty to auto-generate from name. Use lowercase letters, numbers, and hyphens only.</p>
                            </div>
                            <div class="space-y-1.5">
                                <label for="status" class="block text-sm text-slate-700 dark:text-slate-300">Status</label>
                                <select id="status" name="status" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100">
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Inactive dealers are hidden when creating dealer listings.</p>
                            </div>
                            <div class="space-y-1.5">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="show_homepage" value="1" {{ old('show_homepage') ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-700 text-emerald-500 focus:ring-emerald-500">
                                    <span>Show on homepage popular agents section</span>
                                </label>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Default is No (unchecked).</p>
                            </div>
                            <div class="space-y-1.5">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="show_homepage_ad" value="1" {{ old('show_homepage_ad') ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-700 text-emerald-500 focus:ring-emerald-500">
                                    <span>Show on homepage Ad</span>
                                </label>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Shows dealer photo in the “Find a Trusted Agent” banner (max 5). Default is No (unchecked).</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="email" class="block text-sm text-slate-700 dark:text-slate-300">Email</label>
                                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="phone" class="block text-sm text-slate-700 dark:text-slate-300">Phone</label>
                                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="whatsapp" class="block text-sm text-slate-700 dark:text-slate-300">WhatsApp</label>
                                    <input id="whatsapp" name="whatsapp" type="text" value="{{ old('whatsapp') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="mobile" class="block text-sm text-slate-700 dark:text-slate-300">Mobile</label>
                                    <input id="mobile" name="mobile" type="text" value="{{ old('mobile') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" />
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label for="info_detail" class="block text-sm text-slate-700 dark:text-slate-300">Short description (team page)</label>
                                <textarea id="info_detail" name="info_detail" rows="3" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="Brief bio or description shown on the Our Team page.">{{ old('info_detail') }}</textarea>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Shown on the team page below the name. No contact info is displayed there.</p>
                            </div>
                            <div class="space-y-1.5">
                                <label for="address" class="block text-sm text-slate-700 dark:text-slate-300">Address</label>
                                <textarea id="address" name="address" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100">{{ old('address') }}</textarea>
                            </div>
                            @php
                                $states = $states ?? collect();
                                $currentState = old('state', 'Punjab');
                                $currentCity = old('city', 'Lahore');
                                $defaultStateModel = $states->firstWhere('name', $currentState) ?? $states->first();
                                $cityOptions = $defaultStateModel ? $defaultStateModel->cities : collect();
                                $cityNames = $cityOptions->pluck('name')->toArray();
                            @endphp
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="dealer-state-select" class="block text-sm text-slate-700 dark:text-slate-300">State (Pakistan)</label>
                                    <select name="state" id="dealer-state-select" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100">
                                        @foreach($states as $st)
                                            <option value="{{ $st->name }}" {{ $currentState === $st->name ? 'selected' : '' }}>{{ $st->name }}</option>
                                        @endforeach
                                        @if($currentState && !$states->pluck('name')->contains($currentState))
                                            <option value="{{ $currentState }}" selected>{{ $currentState }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="dealer-city-select" class="block text-sm text-slate-700 dark:text-slate-300">City</label>
                                    <select name="city" id="dealer-city-select" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100">
                                        @foreach($cityOptions as $c)
                                            <option value="{{ $c->name }}" {{ $currentCity === $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endforeach
                                        @if($currentCity && !in_array($currentCity, $cityNames))
                                            <option value="{{ $currentCity }}" selected>{{ $currentCity }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors mt-6">
                                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-4">Banner</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Recommended 1920×540px (wide hero). Full-width banner on team profile page; desktop uses it as hero background when no portrait is uploaded.</p>
                                <input type="file" name="banner_image" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" />
                            </div>
                            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors mt-6">
                                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-4">SEO (meta tags)</h2>
                                <div class="space-y-4">
                                    <div class="space-y-1.5">
                                        <label for="meta_title" class="block text-sm text-slate-700 dark:text-slate-300">Meta title</label>
                                        <input id="meta_title" name="meta_title" type="text" value="{{ old('meta_title') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="Optional" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="meta_description" class="block text-sm text-slate-700 dark:text-slate-300">Meta description</label>
                                        <textarea id="meta_description" name="meta_description" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="Optional">{{ old('meta_description') }}</textarea>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="meta_keywords" class="block text-sm text-slate-700 dark:text-slate-300">Meta keywords</label>
                                        <input id="meta_keywords" name="meta_keywords" type="text" value="{{ old('meta_keywords') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="Comma separated, optional" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="canonical_url" class="block text-sm text-slate-700 dark:text-slate-300">Canonical URL</label>
                                        <input id="canonical_url" name="canonical_url" type="text" value="{{ old('canonical_url') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="Optional" />
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition mt-6">Save dealer</button>
                        </form>
                    </div>
                </section>
            </main>
        </div>
        <script>
        (function() {
            var stateEl = document.getElementById('dealer-state-select');
            var cityEl = document.getElementById('dealer-city-select');
            if (stateEl && cityEl && typeof TomSelect !== 'undefined') {
                var citiesByState = @json($states->keyBy('name')->map(fn($s) => $s->cities->pluck('name')->toArray())->toArray());
                var stateSelect = new TomSelect(stateEl, { create: false, sortField: { field: 'text', direction: 'asc' } });
                var citySelect = new TomSelect(cityEl, { create: false, sortField: { field: 'text', direction: 'asc' } });
                stateSelect.on('change', function(val) {
                    var cities = citiesByState[val] || [];
                    citySelect.clearOptions();
                    cities.forEach(function(c) { citySelect.addOption({ value: c, text: c }); });
                    citySelect.clear(true);
                    if (cities.indexOf('Lahore') >= 0) citySelect.setValue('Lahore');
                    else if (cities.length) citySelect.setValue(cities[0]);
                });
            }
        })();
        </script>
    </body>
</html>
