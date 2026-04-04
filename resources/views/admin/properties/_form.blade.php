@php
    $states = $states ?? collect();
    $currentState = old('state', $property->state ?? 'Punjab');
    $currentCity = old('city', $property->city ?? 'Lahore');
    $citiesByState = $states->keyBy('name')->map(fn($s) => $s->cities->pluck('name')->toArray())->toArray();
    $listingTabs = [
        'tab-basic' => 'Basic',
        'tab-status' => 'Status',
        'tab-featured-image' => 'Featured image',
        'tab-address' => 'Address',
        'tab-videos' => 'Video',
        'tab-gallery' => 'Image gallery',
        'tab-video-gallery' => 'Video gallery',
        'tab-price' => 'Price',
        'tab-property-type' => 'Property type & area',
        'tab-features' => 'Features & nearby',
        'tab-seo' => 'SEO',
        'tab-amenities' => 'Amenities',
    ];
@endphp

<div class="flex flex-col lg:flex-row gap-6 lg:gap-8" id="listing-form-tabs">
    <nav class="project-vertical-tabs lg:w-56 flex-shrink-0 border border-slate-200 dark:border-slate-800 rounded-xl bg-white dark:bg-slate-900/80 p-2 shadow-sm lg:sticky lg:top-24 self-start">
        <div class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider px-2 py-1.5 mb-1">Sections</div>
        @foreach($listingTabs as $id => $label)
            <button type="button" class="project-tab-btn w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $loop->first ? 'active' : '' }}" data-tab="{{ $id }}" role="tab">{{ $label }}</button>
        @endforeach
    </nav>
    <div class="listing-tab-content flex-1 min-w-0 space-y-0">
        {{-- 1. Basic --}}
        <div id="tab-basic" class="tab-panel active rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg transition-colors p-6 mb-6" role="tabpanel">
            <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">1. Basic</h2>
            @if($listingType === 'dealer' && $dealers->isNotEmpty())
                <div class="mb-4">
                    <label for="dealer_id" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Dealer</label>
                    <select name="dealer_id" id="dealer_id" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                        <option value="">Select dealer</option>
                        @foreach($dealers as $d)
                            <option value="{{ $d->id }}" {{ old('dealer_id', $property->dealer_id ?? request('dealer')) == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                    @error('dealer_id')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Title *</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $property->title) }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                    @error('title')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 hidden" id="title-error-inline" data-error-for="title"></p>
                </div>
                <div>
                    <label for="slug" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Slug (optional, auto from title)</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $property->slug) }}" placeholder="Auto from title if empty" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                    @error('slug')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">Project types</label>
                    @php $selectedTypeIds = old('project_type_ids', $property->projectTypes->pluck('id')->toArray() ?? []); @endphp
                    <div class="flex flex-wrap gap-3">
                        @foreach($projectTypes as $pt)
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="project_type_ids[]" value="{{ $pt->id }}" {{ in_array($pt->id, (array) $selectedTypeIds) ? 'checked' : '' }} class="rounded border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-950 text-emerald-500" />
                                <span class="text-sm text-slate-700 dark:text-slate-300">{{ $pt->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @if($projectTypes->isEmpty())
                        <p class="text-xs text-slate-500 mt-1">No project types. <a href="{{ route('admin.project_types.index') }}" class="text-emerald-600 dark:text-emerald-400">Add types</a> first.</p>
                    @endif
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-slate-700 dark:text-slate-300 mb-2">Purpose</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="purpose" value="sale" {{ old('purpose', $property->purpose ?? 'sale') === 'sale' ? 'checked' : '' }} class="text-emerald-500 border-slate-400" />
                            <span class="text-sm text-slate-700 dark:text-slate-300">Sale</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="purpose" value="rent" {{ old('purpose', $property->purpose ?? 'sale') === 'rent' ? 'checked' : '' }} class="text-emerald-500 border-slate-400" />
                            <span class="text-sm text-slate-700 dark:text-slate-300">Rent</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <label for="description" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Description</label>
                <textarea id="description" name="description" rows="4" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ old('description', $property->description) }}</textarea>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-status">Next</button>
            </div>
        </div>

        {{-- 2. Status --}}
        <div id="tab-status" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg transition-colors p-6 mb-6 hidden" role="tabpanel">
            <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">2. Status</h2>
            <div>
                <label class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Listing status</label>
                <select name="status" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                    <option value="active" {{ old('status', $property->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="hold" {{ old('status', $property->status ?? 'active') === 'hold' ? 'selected' : '' }}>Hold</option>
                    <option value="inactive" {{ old('status', $property->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="close" {{ old('status', $property->status ?? 'active') === 'close' ? 'selected' : '' }}>Close</option>
                </select>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Only active listings are counted on the dashboard and can be displayed on the frontend.</p>
            </div>
            <div class="mt-4">
                <label class="inline-flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_hot" value="1" class="mt-1 rounded border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-950 text-emerald-500" {{ old('is_hot', $property->is_hot ?? true) ? 'checked' : '' }} />
                    <span>
                        <span class="block text-sm font-medium text-slate-800 dark:text-slate-200">Hot listing (portal homepage)</span>
                        <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5">When enabled, this listing can appear in the “Browse Hot Properties” section on the portal. Default is on.</span>
                    </span>
                </label>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-basic">Back</button>
                <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-featured-image">Next</button>
            </div>
        </div>

        {{-- 3. Featured image --}}
        <div id="tab-featured-image" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg transition-colors p-6 mb-6 hidden" role="tabpanel">
            <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">3. Featured image</h2>
            @if($property->featured_image ?? null)
                <div class="mb-2 flex items-center gap-3 flex-wrap">
                    <img src="{{ asset('storage/' . $property->featured_image) }}" alt="" class="h-20 rounded border border-slate-300 dark:border-slate-700" />
                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer"><input type="checkbox" name="remove_featured_image" value="1" class="rounded border-slate-400" /> Remove</label>
                </div>
            @endif
            <input type="file" name="featured_image" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" />
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-status">Back</button>
                <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-address">Next</button>
            </div>
        </div>

        {{-- 4. Address --}}
        <div id="tab-address" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg transition-colors p-6 mb-6 hidden" role="tabpanel">
            <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">4. Address</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="property-state-select" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">State (Pakistan)</label>
                    <select name="state" id="property-state-select" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                        @foreach($states as $st)
                            <option value="{{ $st->name }}" {{ $currentState == $st->name ? 'selected' : '' }}>{{ $st->name }}</option>
                        @endforeach
                        @if($currentState && !$states->pluck('name')->contains($currentState))
                            <option value="{{ $currentState }}" selected>{{ $currentState }}</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label for="property-city-select" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">City</label>
                    @php
                        $defaultStateModel = $states->firstWhere('name', $currentState) ?? $states->first();
                        $cityOptions = $defaultStateModel ? $defaultStateModel->cities : collect();
                        $cityNames = $cityOptions->pluck('name')->toArray();
                    @endphp
                    <select name="city" id="property-city-select" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                        @foreach($cityOptions as $c)
                            <option value="{{ $c->name }}" {{ $currentCity == $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                        @if($currentCity && !in_array($currentCity, $cityNames))
                            <option value="{{ $currentCity }}" selected>{{ $currentCity }}</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="address" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Address</label>
                    <textarea id="address" name="address" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ old('address', $property->address) }}</textarea>
                </div>
                <div>
                    <label for="short_address" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Short address</label>
                    <input id="short_address" name="short_address" type="text" value="{{ old('short_address', $property->short_address) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                </div>
                <div>
                    <label for="town" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Town</label>
                    <input id="town" name="town" type="text" value="{{ old('town', $property->town) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Latitude</label>
                        <input id="latitude" name="latitude" type="text" value="{{ old('latitude', $property->latitude) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                    </div>
                    <div>
                        <label for="longitude" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Longitude</label>
                        <input id="longitude" name="longitude" type="text" value="{{ old('longitude', $property->longitude) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                    </div>
                </div>
                <div>
                    <label for="google_map" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Google Map (URL or embed)</label>
                    <textarea id="google_map" name="google_map" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ old('google_map', $property->google_map) }}</textarea>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-featured-image">Back</button>
                <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-videos">Next</button>
            </div>
        </div>

        {{-- 4. Video (single) --}}
        <div id="tab-videos" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg transition-colors p-6 mb-6 hidden" role="tabpanel">
            <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">4. Video (YouTube embed code)</h2>
            <p class="text-xs text-slate-500 mb-3">One main video embed code.</p>
            @php
                $videosArr = old('videos', $property->videos ?? []);
                $videosArr = is_array($videosArr) ? $videosArr : [];
                $singleVideo = $videosArr[0] ?? '';
            @endphp
            <textarea name="videos[]" rows="4" placeholder="Paste YouTube embed code here" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ is_array($singleVideo) ? '' : $singleVideo }}</textarea>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-address">Back</button>
                <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-gallery">Next</button>
            </div>
        </div>

        {{-- 5. Image gallery --}}
        <div id="tab-gallery" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg transition-colors p-6 mb-6 hidden" role="tabpanel">
            <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">5. Image gallery</h2>
            <div id="property-gallery-remove-container"></div>
            <div id="property-gallery-list" class="space-y-3">
                @php $gallery = $property->gallery ?? []; $gallery = is_array($gallery) ? collect($gallery)->sortBy('order')->values()->all() : []; @endphp
                @foreach($gallery as $idx => $g)
                    @php $path = is_array($g) ? ($g['path'] ?? '') : $g; @endphp
                    <div class="property-gallery-item flex gap-3 items-center flex-wrap gallery-item-row">
                        <input type="hidden" name="gallery_paths[]" value="{{ $path }}" />
                        <input type="number" name="gallery_order[]" value="{{ $idx }}" min="0" class="w-16 rounded border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-2 py-1 text-sm" />
                        @if($path)
                            <img src="{{ asset('storage/' . $path) }}" alt="" class="h-14 w-20 object-cover rounded border border-slate-300 dark:border-slate-700" loading="lazy" />
                        @endif
                        <button type="button" class="property-remove-gallery px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-400 text-xs hover:bg-rose-600/10" data-path="{{ $path }}">Remove</button>
                    </div>
                @endforeach
            </div>
            <div class="mt-3">
                <label class="block text-xs text-slate-500 mb-1">Add new images</label>
                <input type="file" name="gallery_images[]" multiple accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5" />
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-videos">Back</button>
                <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-video-gallery">Next</button>
            </div>
        </div>

        {{-- 6. Video gallery --}}
        <div id="tab-video-gallery" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg transition-colors p-6 mb-6 hidden" role="tabpanel">
            <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">6. Video gallery (YouTube embed codes)</h2>
            <div id="property-video-gallery-container" class="space-y-3">
                @php $vg = old('video_gallery', $property->video_gallery ?? []); $vg = is_array($vg) ? $vg : []; @endphp
                @foreach(array_merge($vg, ['']) as $v)
                    <div class="property-vg-row flex gap-2 items-start">
                        <textarea name="video_gallery[]" rows="2" placeholder="Embed code" class="flex-1 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ is_array($v) ? '' : $v }}</textarea>
                        <button type="button" class="property-remove-vg text-rose-600 dark:text-rose-400 text-xs hover:text-rose-500 px-2 py-1 rounded border border-rose-600/60">Remove</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="property-add-video-gallery" class="mt-2 text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-500">+ Add video</button>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-gallery">Back</button>
                <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-price">Next</button>
            </div>
        </div>

        {{-- 7. Price --}}
        <div id="tab-price" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg transition-colors p-6 mb-6 hidden" role="tabpanel">
            <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">7. Price</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="price_string" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Price (string, e.g. 45 Lac)</label>
                    <input id="price_string" name="price_string" type="text" value="{{ old('price_string', $property->price_string) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                </div>
                <div>
                    <label for="price_digits" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Price (digits)</label>
                    <input id="price_digits" name="price_digits" type="number" step="any" min="0" value="{{ old('price_digits', $property->price_digits) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-video-gallery">Back</button>
                <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-property-type">Next</button>
            </div>
        </div>

        {{-- 8. Property type & area --}}
        <div id="tab-property-type" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg transition-colors p-6 mb-6 hidden" role="tabpanel">
            <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">8. Property type & area</h2>
            <div class="space-y-4">
                <div>
                    <label for="property_type" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Property type</label>
                    <select name="property_type" id="property_type" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                        <option value="">—</option>
                        <option value="plot" {{ old('property_type', $property->property_type) == 'plot' ? 'selected' : '' }}>Plot</option>
                        <option value="home" {{ old('property_type', $property->property_type) == 'home' ? 'selected' : '' }}>Home</option>
                        <option value="plaza" {{ old('property_type', $property->property_type) == 'plaza' ? 'selected' : '' }}>Plaza</option>
                        <option value="flat" {{ old('property_type', $property->property_type) == 'flat' ? 'selected' : '' }}>Flat</option>
                        <option value="apartment" {{ old('property_type', $property->property_type) == 'apartment' ? 'selected' : '' }}>Apartment</option>
                        <option value="file" {{ old('property_type', $property->property_type) == 'file' ? 'selected' : '' }}>File</option>
                    </select>
                </div>
                <div id="property-type-extra" class="grid grid-cols-2 md:grid-cols-4 gap-4 {{ in_array(old('property_type', $property->property_type), ['home', 'flat']) ? '' : 'hidden' }}">
                    <div>
                        <label for="bedrooms" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Bedrooms</label>
                        <input id="bedrooms" name="bedrooms" type="number" min="0" value="{{ old('bedrooms', $property->bedrooms) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                    </div>
                    <div>
                        <label for="bathrooms" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Bathrooms</label>
                        <input id="bathrooms" name="bathrooms" type="number" min="0" value="{{ old('bathrooms', $property->bathrooms) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                    </div>
                    <div>
                        <label for="garage" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Garage</label>
                        <input id="garage" name="garage" type="number" min="0" value="{{ old('garage', $property->garage) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                    </div>
                    <div>
                        <label for="kitchen" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Kitchen</label>
                        <input id="kitchen" name="kitchen" type="number" min="0" value="{{ old('kitchen', $property->kitchen) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="area_marla" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Area (Marla) — 20 marla = 1 kanal</label>
                        <input id="area_marla" name="area_marla" type="number" step="0.01" min="0" value="{{ old('area_marla', $property->area_marla) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" placeholder="Enter marla to auto-fill kanal" />
                    </div>
                    <div>
                        <label for="area_kanal" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Area (Kanal)</label>
                        <input id="area_kanal" name="area_kanal" type="number" step="0.01" min="0" value="{{ old('area_kanal', $property->area_kanal) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" placeholder="Enter kanal to auto-fill marla" />
                    </div>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-price">Back</button>
                <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-features">Next</button>
            </div>
        </div>

        {{-- 9. Features & nearby --}}
        <div id="tab-features" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg transition-colors p-6 mb-6 hidden" role="tabpanel">
            <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-5">9. Features & nearby</h2>
            <div class="space-y-4">
                @foreach(['features' => 'Features', 'location_accessibility' => 'Location accessibility', 'nearest_hospitals' => 'Nearest hospitals', 'nearest_markets' => 'Nearest markets', 'nearest_restaurants' => 'Nearest restaurants / cafes / bakeries'] as $name => $label)
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/40 p-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">{{ $label }}</label>
                        <div class="property-list-container space-y-2" data-name="{{ $name }}">
                            @php $arr = old($name, $property->$name ?? []); $arr = is_array($arr) ? $arr : []; @endphp
                            @foreach(array_merge($arr, ['']) as $val)
                                <div class="flex gap-2">
                                    <input type="text" name="{{ $name }}[]" value="{{ is_array($val) ? '' : $val }}" placeholder="Title" class="flex-1 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                                    <button type="button" class="property-remove-list px-2 py-1 rounded border border-slate-400 text-slate-600 dark:text-slate-400 text-xs hover:bg-slate-200 dark:hover:bg-slate-700">Remove</button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="property-add-list mt-2 text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-500" data-name="{{ $name }}">+ Add</button>
                    </div>
                @endforeach
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-property-type">Back</button>
                <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-seo">Next</button>
            </div>
        </div>

        {{-- 9b. SEO --}}
        <div id="tab-seo" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg transition-colors p-6 mb-6 hidden" role="tabpanel">
            <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">SEO (meta tags, canonical)</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Meta title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $property->meta_title ?? '') }}" placeholder="Optional, for title and og:title" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                </div>
                <div>
                    <label class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Meta description</label>
                    <textarea name="meta_description" rows="2" placeholder="Optional, for meta description and og:description" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ old('meta_description', $property->meta_description ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Meta keywords</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $property->meta_keywords ?? '') }}" placeholder="Optional, comma-separated" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                </div>
                <div>
                    <label class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Canonical URL</label>
                    <input type="text" name="canonical_url" id="canonical_url" value="{{ old('canonical_url', $property->canonical_url ?? '') }}" placeholder="Optional, full URL for canonical link (e.g. https://...)" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                    @error('canonical_url')
                        <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 hidden" id="canonical_url-error-inline" data-error-for="canonical_url"></p>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-features">Back</button>
                <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-amenities">Next</button>
            </div>
        </div>

        {{-- 10. Amenities --}}
        <div id="tab-amenities" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg transition-colors p-6 mb-6 hidden" role="tabpanel">
            <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">10. Amenities</h2>
            <div class="mb-4">
                <label for="amenities_description" class="block text-sm text-slate-700 dark:text-slate-300 mb-1">Main description</label>
                <textarea id="amenities_description" name="amenities_description" rows="3" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ old('amenities_description', $property->amenities_description) }}</textarea>
            </div>
            <p class="text-xs text-slate-500 mb-3">Options with icon (use icon picker).</p>
            <div id="property-amenities-container" class="space-y-3">
                @php $amenities = old('amenities', $property->amenities ?? []); if (!empty($amenities) && isset($amenities[0]) && is_array($amenities[0])) { $amenityTitles = array_column($amenities, 'title'); $amenityIcons = array_column($amenities, 'icon'); } else { $amenityTitles = []; $amenityIcons = []; } @endphp
                @foreach(array_merge(array_map(null, $amenityTitles ?: [], $amenityIcons ?: []), [['', '']]) as $idx => $a)
                    @if(!is_array($a)) @php $a = [$a, '']; @endphp @endif
                    <div class="property-amenity-row flex gap-2 items-center flex-wrap">
                        <input type="text" name="amenity_titles[]" value="{{ $a[0] ?? '' }}" placeholder="Title" class="flex-1 min-w-[120px] rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                        <div class="flex items-center gap-1">
                            <input type="text" name="amenity_icons[]" value="{{ $a[1] ?? '' }}" placeholder="Icon" id="amenity_icon_{{ $idx }}" class="icon-picker-target w-28 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                            <button type="button" class="icon-picker-btn px-2 py-1.5 rounded border border-slate-400 text-slate-600 dark:text-slate-400 text-xs hover:bg-slate-200 dark:hover:bg-slate-700" data-target="amenity_icon_{{ $idx }}">Pick</button>
                        </div>
                        <button type="button" class="property-remove-amenity px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-400 text-xs">Remove</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="property-add-amenity" class="mt-2 text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-500">+ Add amenity</button>
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-seo">Back</button>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">{{ $property->exists ? 'Update listing' : 'Create listing' }}</button>
            </div>
        </div>
    </div>
</div>
