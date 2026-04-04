@php
    $isEdit = isset($project) && $project && $project->exists;
    $project = $project ?? new \App\Models\Project();
    $projectTypes = $projectTypes ?? collect();
    $states = $states ?? collect();
    $currentState = old('state', $project->state ?? 'Punjab');
    $currentCity = old('city', $project->city ?? 'Lahore');
    $citiesByState = $states->keyBy('name')->map(fn($s) => $s->cities->pluck('name')->toArray())->toArray();
    $projectTabs = [
        'tab-basics' => 'Basics',
        'tab-status' => 'Status',
        'tab-address' => 'Address',
        'tab-media' => 'Project media',
        'tab-featured-video' => 'Featured video',
        'tab-about' => 'About developers',
        'tab-pdf' => 'Project file (PDF)',
        'tab-noc' => 'NOC & planning',
        'tab-future-note' => 'Future note',
        'tab-extra' => 'Extra section',
        'tab-features' => 'Unique features',
        'tab-price-plan' => 'Price plan',
        'tab-faqs' => 'FAQs',
        'tab-plans' => 'Plans',
        'tab-title-desc' => 'Title + descriptions',
        'tab-videos' => 'Videos',
        'tab-gallery' => 'Gallery',
        'tab-seo' => 'SEO',
    ];
    $tabIds = array_keys($projectTabs);
@endphp

<div class="flex flex-col lg:flex-row gap-6 lg:gap-8" id="project-form-tabs">
    <nav class="project-vertical-tabs lg:w-56 flex-shrink-0 border border-slate-200 dark:border-slate-800 rounded-xl bg-white dark:bg-slate-900/80 p-2 shadow-sm lg:sticky lg:top-24 self-start">
        <div class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider px-2 py-1.5 mb-1">Sections</div>
        @foreach($projectTabs as $id => $label)
            <button type="button" class="project-tab-btn w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $loop->first ? 'active' : '' }}" data-tab="{{ $id }}" role="tab">
                {{ $label }}
            </button>
        @endforeach
    </nav>
    <div class="project-tab-content flex-1 min-w-0 space-y-0">
{{-- 1. Basics --}}
<div id="tab-basics" class="tab-panel active rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">1. Basics</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-2">Project types (select multiple)</label>
            <div class="flex flex-wrap gap-3">
                @php $selectedIds = old('project_type_ids', $project->projectTypes->pluck('id')->toArray() ?? []); @endphp
                @foreach ($projectTypes as $pt)
                    <label class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-700 px-3 py-2 bg-slate-50 dark:bg-slate-950/60 cursor-pointer hover:border-slate-400 dark:hover:border-slate-600 transition-colors">
                        <input type="checkbox" name="project_type_ids[]" value="{{ $pt->id }}" {{ in_array($pt->id, $selectedIds) ? 'checked' : '' }} class="rounded border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-950 text-emerald-500" />
                        <span class="text-sm text-slate-700 dark:text-slate-200">{{ $pt->name }}</span>
                    </label>
                @endforeach
            </div>
            @if($projectTypes->isEmpty())
                <p class="text-xs text-slate-500 dark:text-slate-500 mt-1">No project types yet. <a href="{{ route('admin.project_types.index') }}" class="text-emerald-600 dark:text-emerald-400">Create types</a> first.</p>
            @endif
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Title *</label>
            <input type="text" name="title" id="project_title" value="{{ old('title', $project->title ?? '') }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
            @error('title')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 hidden" id="project-title-error-inline" data-error-for="title"></p>
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $project->slug ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" placeholder="Auto from title if empty" />
            @error('slug')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Price (string)</label>
            <input type="text" name="price" value="{{ old('price', $project->price ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" placeholder="e.g. On Request" />
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Description</label>
            <textarea name="description" rows="4" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ old('description', $project->description ?? '') }}</textarea>
        </div>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-status">Next</button>
    </div>
</div>

{{-- 2. Status --}}
<div id="tab-status" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">2. Status</h2>
    <div>
        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Project status</label>
        <select name="status" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
            <option value="active" {{ old('status', $project->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="hold" {{ old('status', $project->status ?? 'active') === 'hold' ? 'selected' : '' }}>Hold</option>
            <option value="inactive" {{ old('status', $project->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="close" {{ old('status', $project->status ?? 'active') === 'close' ? 'selected' : '' }}>Close</option>
        </select>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Only active projects are shown on the dashboard and can be displayed on the frontend.</p>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-basics">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-address">Next</button>
    </div>
</div>

{{-- 3. Address --}}
<div id="tab-address" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">3. Address</h2>
    <script>
        window.citiesByState = @json($citiesByState);
        window.initialState = @json($currentState);
        window.initialCity = @json($currentCity);
    </script>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">State (Pakistan)</label>
            <select name="state" id="state-select" class="state-city-select block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                @foreach($states as $st)
                    <option value="{{ $st->name }}" {{ $currentState === $st->name ? 'selected' : '' }}>{{ $st->name }}</option>
                @endforeach
                @if($currentState && !$states->pluck('name')->contains($currentState))
                    <option value="{{ $currentState }}" selected>{{ $currentState }}</option>
                @endif
            </select>
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">City</label>
            <select name="city" id="city-select" class="state-city-select block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                @php
                    $defaultStateModel = $states->firstWhere('name', $currentState) ?? $states->first();
                    $cityOptions = $defaultStateModel ? $defaultStateModel->cities : collect();
                    $cityNames = $cityOptions->pluck('name')->toArray();
                @endphp
                @foreach($cityOptions as $c)
                    <option value="{{ $c->name }}" {{ $currentCity === $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
                @if($currentCity && !in_array($currentCity, $cityNames))
                    <option value="{{ $currentCity }}" selected>{{ $currentCity }}</option>
                @endif
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Short address</label>
            <input type="text" name="short_address" value="{{ old('short_address', $project->short_address ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Full address</label>
            <textarea name="full_address" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ old('full_address', $project->full_address ?? '') }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Google Map (URL or embed code)</label>
            <textarea name="google_map" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 font-mono text-xs">{{ old('google_map', $project->google_map ?? '') }}</textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:col-span-2">
            <div>
                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Latitude</label>
                <input type="text" name="latitude" value="{{ old('latitude', $project->latitude ?? '') }}" placeholder="e.g. 31.5204" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500" />
            </div>
            <div>
                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Longitude</label>
                <input type="text" name="longitude" value="{{ old('longitude', $project->longitude ?? '') }}" placeholder="e.g. 74.3587" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500" />
            </div>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Address image</label>
            @if($isEdit && $project->address_image)
                <div class="mb-2 flex items-center gap-3 flex-wrap">
                    <img src="{{ asset('storage/' . $project->address_image) }}" alt="Address" class="h-20 w-20 object-cover rounded-lg border border-slate-300 dark:border-slate-700" />
                    <a href="{{ asset('storage/' . $project->address_image) }}" target="_blank" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">Open full size</a>
                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer"><input type="checkbox" name="remove_address_image" value="1" class="rounded border-slate-400" /> Remove image</label>
                </div>
            @endif
            <input type="file" name="address_image" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" />
        </div>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-status">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-media">Next</button>
    </div>
</div>

{{-- 4. Media --}}
<div id="tab-media" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">3. Project media</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Logo</label>
            @if($isEdit && $project->logo)
                <div class="mb-2 flex items-center gap-3 flex-wrap">
                    <img src="{{ asset('storage/' . $project->logo) }}" alt="Logo" class="h-20 w-20 object-cover rounded-lg border border-slate-300 dark:border-slate-700" />
                    <a href="{{ asset('storage/' . $project->logo) }}" target="_blank" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">Open full size</a>
                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer"><input type="checkbox" name="remove_logo" value="1" class="rounded border-slate-400" /> Remove</label>
                </div>
            @endif
            <input type="file" name="logo" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" />
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Featured image</label>
            @if($isEdit && $project->featured_image)
                <div class="mb-2 flex items-center gap-3 flex-wrap">
                    <img src="{{ asset('storage/' . $project->featured_image) }}" alt="Featured" class="h-20 w-20 object-cover rounded-lg border border-slate-300 dark:border-slate-700" />
                    <a href="{{ asset('storage/' . $project->featured_image) }}" target="_blank" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">Open full size</a>
                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer"><input type="checkbox" name="remove_featured_image" value="1" class="rounded border-slate-400" /> Remove</label>
                </div>
            @endif
            <input type="file" name="featured_image" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" />
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Homepage listing image</label>
            @if($isEdit && $project->homepage_listing_image)
                <div class="mb-2 flex items-center gap-3 flex-wrap">
                    <img src="{{ asset('storage/' . $project->homepage_listing_image) }}" alt="Homepage" class="h-20 w-20 object-cover rounded-lg border border-slate-300 dark:border-slate-700" />
                    <a href="{{ asset('storage/' . $project->homepage_listing_image) }}" target="_blank" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">Open full size</a>
                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer"><input type="checkbox" name="remove_homepage_listing_image" value="1" class="rounded border-slate-400" /> Remove</label>
                </div>
            @endif
            <input type="file" name="homepage_listing_image" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" />
        </div>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-address">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-featured-video">Next</button>
    </div>
</div>

{{-- 4. Featured video --}}
<div id="tab-featured-video" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">4. Featured YouTube video</h2>
    <div class="space-y-4">
        @if($isEdit && ($project->featured_youtube_url || $project->featured_video_title || $project->featured_video_description))
            <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer mb-2"><input type="checkbox" name="remove_featured_video" value="1" class="rounded border-slate-400" /> Remove featured video</label>
        @endif
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Embed code</label>
            <textarea name="featured_youtube_url" rows="4" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 font-mono placeholder-slate-400 dark:placeholder-slate-500" placeholder="Paste iframe embed code from YouTube (Share → Embed)">{{ old('featured_youtube_url', $project->featured_youtube_url ?? '') }}</textarea>
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Video title</label>
            <input type="text" name="featured_video_title" value="{{ old('featured_video_title', $project->featured_video_title ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Video description (rich text)</label>
            <div class="richtext-wrap bg-slate-50 dark:bg-slate-950/60 rounded-lg border border-slate-300 dark:border-slate-700 min-h-[120px]"><textarea name="featured_video_description" id="featured_video_description" rows="5" class="richtext hidden">{{ old('featured_video_description', $project->featured_video_description ?? '') }}</textarea></div>
        </div>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-media">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-about">Next</button>
    </div>
</div>

{{-- 5. About --}}
<div id="tab-about" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">5. About developers</h2>
    @if($isEdit && $project->developer_logo)
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-2 flex items-center gap-3 flex-wrap">
            <img src="{{ asset('storage/' . $project->developer_logo) }}" alt="Developer logo" class="h-16 object-contain rounded border border-slate-200 dark:border-slate-600" />
            <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer"><input type="checkbox" name="remove_developer_logo" value="1" class="rounded border-slate-400" /> Remove developer logo</label>
        </p>
    @endif
    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Developer logo / image</label>
    <input type="file" name="developer_logo" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200 mb-4" />
    <textarea name="about_developers" rows="4" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ old('about_developers', $project->about_developers ?? '') }}</textarea>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-featured-video">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-pdf">Next</button>
    </div>
</div>

{{-- 6. PDF --}}
<div id="tab-pdf" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">6. Project file (PDF)</h2>
    @if($isEdit && $project->project_file_pdf)
        <p class="text-xs text-slate-500 mb-2 flex items-center gap-3 flex-wrap">
            <a href="{{ asset('storage/' . $project->project_file_pdf) }}" target="_blank" class="text-emerald-600 dark:text-emerald-400">Current PDF</a>
            <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer"><input type="checkbox" name="remove_project_file_pdf" value="1" class="rounded border-slate-400" /> Remove PDF</label>
        </p>
    @endif
    <input type="file" name="project_file_pdf" accept=".pdf,application/pdf" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" />
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-about">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-noc">Next</button>
    </div>
</div>

{{-- 7. NOC --}}
<div id="tab-noc" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">7. NOC & planning approved</h2>
    <div class="space-y-4">
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Content (rich text)</label>
            <div class="richtext-wrap bg-slate-50 dark:bg-slate-950/60 rounded-lg border border-slate-300 dark:border-slate-700 min-h-[100px]"><textarea name="noc_planning_content" id="noc_planning_content" rows="4" class="richtext hidden">{{ old('noc_planning_content', $project->noc_planning_content ?? '') }}</textarea></div>
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Image</label>
            @if($isEdit && $project->noc_planning_image)
                <div class="mb-2 flex items-center gap-3 flex-wrap">
                    <img src="{{ asset('storage/' . $project->noc_planning_image) }}" alt="NOC" class="h-20 w-20 object-cover rounded-lg border border-slate-300 dark:border-slate-700" />
                    <a href="{{ asset('storage/' . $project->noc_planning_image) }}" target="_blank" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">Open full size</a>
                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer"><input type="checkbox" name="remove_noc_planning_image" value="1" class="rounded border-slate-400" /> Remove</label>
                </div>
            @endif
            <input type="file" name="noc_planning_image" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" />
        </div>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-pdf">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-future-note">Next</button>
    </div>
</div>

{{-- 8. Future note --}}
<div id="tab-future-note" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">8. Future note</h2>
    <div class="space-y-4">
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Title</label>
            <input type="text" name="future_note_title" value="{{ old('future_note_title', $project->future_note_title ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Content</label>
            <textarea name="future_note_content" rows="3" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ old('future_note_content', $project->future_note_content ?? '') }}</textarea>
        </div>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-noc">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-extra">Next</button>
    </div>
</div>

{{-- 9. Extra --}}
<div id="tab-extra" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">9. Extra section (title + rich text)</h2>
    <div class="space-y-4">
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Section title</label>
            <input type="text" name="extra_section_title" value="{{ old('extra_section_title', $project->extra_section_title ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Section content (rich text)</label>
            <div class="richtext-wrap bg-slate-50 dark:bg-slate-950/60 rounded-lg border border-slate-300 dark:border-slate-700 min-h-[100px]"><textarea name="extra_section_content" id="extra_section_content" rows="4" class="richtext hidden">{{ old('extra_section_content', $project->extra_section_content ?? '') }}</textarea></div>
        </div>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-future-note">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-features">Next</button>
    </div>
</div>

{{-- 10. Features --}}
<div id="tab-features" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">10. Unique features</h2>
    <p class="text-xs text-slate-500 mb-3">Add features with title and icon name (e.g. home, check, star from Heroicons).</p>
    <div id="features-container" class="space-y-3">
        @php
            $featureTitles = old('feature_titles', []);
            $featureIcons = old('feature_icons', []);
            $existingFeatures = $project->unique_features ?? [];
            if (empty($featureTitles) && !empty($existingFeatures)) {
                $featureTitles = array_column($existingFeatures, 'title');
                $featureIcons = array_column($existingFeatures, 'icon');
            }
            $featureTitles = array_merge($featureTitles ?: [], ['']);
            $featureIcons = array_merge($featureIcons ?: [], ['']);
        @endphp
        @foreach($featureTitles as $idx => $title)
            <div class="flex gap-2 items-center feature-row">
                <input type="text" name="feature_titles[]" value="{{ $title }}" placeholder="Title" class="flex-1 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                <input type="text" name="feature_icons[]" value="{{ $featureIcons[$idx] ?? '' }}" placeholder="Icon name" id="feature_icon_{{ $idx }}" class="w-32 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                <button type="button" class="icon-picker-btn px-2 py-1 rounded border border-slate-400 text-slate-600 dark:text-slate-400 text-xs hover:bg-slate-200 dark:hover:bg-slate-700" data-target="feature_icon_{{ $idx }}">Pick</button>
                <button type="button" class="remove-feature text-rose-600 dark:text-rose-400 hover:text-rose-500 dark:hover:text-rose-300 text-xs">Remove</button>
            </div>
        @endforeach
    </div>
    <button type="button" id="add-feature" class="mt-2 text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300">+ Add feature</button>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-extra">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-price-plan">Next</button>
    </div>
</div>

{{-- 11. Price plan --}}
<div id="tab-price-plan" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">11. Price plan</h2>
    <div class="space-y-4">
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Section title</label>
            <input type="text" name="price_plan_section_title" value="{{ old('price_plan_section_title', $project->price_plan_section_title ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Plan items (one per line or add rows)</label>
            <div id="price-plan-container" class="space-y-2">
                @php $planItems = old('price_plan_items', $project->price_plan_items ?? []); @endphp
                @foreach(array_merge($planItems ?: [], ['']) as $item)
                    <input type="text" name="price_plan_items[]" value="{{ is_array($item) ? '' : $item }}" placeholder="e.g. 2 BHK from 45 Lac" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                @endforeach
            </div>
            <button type="button" id="add-price-plan" class="mt-2 text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300">+ Add row</button>
        </div>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-features">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-faqs">Next</button>
    </div>
</div>

{{-- 12. FAQs --}}
<div id="tab-faqs" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">12. FAQs</h2>
    <div id="faqs-container" class="space-y-4">
        @php $faqs = old('faq_questions', $project->faqs ?? []); if (!empty($faqs) && isset($faqs[0]) && is_array($faqs[0])) { $faqQs = array_column($faqs, 'question'); $faqAs = array_column($faqs, 'answer'); } else { $faqQs = $faqs; $faqAs = old('faq_answers', array_column($project->faqs ?? [], 'answer')); } @endphp
        @foreach(array_merge(array_map(null, $faqQs ?: [], $faqAs ?: []), [['', '']]) as $idx => $faq)
            @if(!is_array($faq)) @php $faq = [$faq, '']; @endphp @endif
            <div class="faq-row border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-2">
                <input type="text" name="faq_questions[]" value="{{ $faq[0] ?? '' }}" placeholder="Question" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                <textarea name="faq_answers[]" rows="2" placeholder="Answer" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ $faq[1] ?? '' }}</textarea>
                <button type="button" class="remove-faq text-rose-600 dark:text-rose-400 hover:text-rose-500 dark:hover:text-rose-300 text-xs">Remove FAQ</button>
            </div>
        @endforeach
    </div>
    <button type="button" id="add-faq" class="mt-2 text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300">+ Add FAQ</button>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-price-plan">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-plans">Next</button>
    </div>
</div>

{{-- 13. Plans --}}
<div id="tab-plans" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">13. Plans (image + title)</h2>
    <div id="plans-container" class="space-y-4">
        @php $plans = old('plan_titles', $project->plans ?? []); @endphp
        @if(!empty($plans))
            @foreach($plans as $idx => $p)
                @php $pt = is_array($p) ? ($p['title'] ?? '') : $p; $img = is_array($p) ? ($p['image'] ?? '') : ''; @endphp
                <div class="plan-row border border-slate-200 dark:border-slate-700 rounded-lg p-3 flex flex-wrap gap-3 items-end">
                    <input type="hidden" name="existing_plan_images[]" value="{{ $img }}" />
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Title</label>
                        <input type="text" name="plan_titles[]" value="{{ $pt }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Image</label>
                        @if($img)
                            <div class="mb-2 flex items-center gap-3">
                                <img src="{{ asset('storage/' . $img) }}" alt="Plan" class="h-16 w-16 object-cover rounded-lg border border-slate-300 dark:border-slate-700" />
                                <a href="{{ asset('storage/' . $img) }}" target="_blank" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">Open</a>
                            </div>
                        @endif
                        <input type="file" name="plan_images[]" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" />
                    </div>
                    <button type="button" class="remove-plan text-rose-600 dark:text-rose-400 hover:text-rose-500 dark:hover:text-rose-300 text-xs">Remove</button>
                </div>
            @endforeach
        @else
            <div class="plan-row border border-slate-200 dark:border-slate-700 rounded-lg p-3 flex flex-wrap gap-3 items-end">
                <input type="hidden" name="existing_plan_images[]" value="" />
                <div class="flex-1 min-w-[200px]"><label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Title</label><input type="text" name="plan_titles[]" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" /></div>
                <div class="flex-1 min-w-[200px]"><label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Image</label><input type="file" name="plan_images[]" accept="image/*" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" /></div>
                <button type="button" class="remove-plan text-rose-600 dark:text-rose-400 hover:text-rose-500 dark:hover:text-rose-300 text-xs">Remove</button>
            </div>
        @endif
    </div>
    <button type="button" id="add-plan" class="mt-2 text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300">+ Add plan</button>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-faqs">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-title-desc">Next</button>
    </div>
</div>

{{-- 14. Title + descriptions --}}
<div id="tab-title-desc" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">14. Section: multiple title + description</h2>
    <div class="space-y-4">
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Section title</label>
            <input type="text" name="td_section_title" value="{{ old('td_section_title', $project->title_descriptions['section_title'] ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Section description</label>
            <textarea name="td_section_description" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ old('td_section_description', $project->title_descriptions['section_description'] ?? '') }}</textarea>
        </div>
        <div id="td-container" class="space-y-3">
            @php
                $tdTitles = old('td_titles', []);
                $tdDescs = old('td_descriptions', []);
                $tdExisting = $project->title_descriptions['items'] ?? [];
                if (empty($tdTitles) && !empty($tdExisting)) {
                    $tdTitles = array_column($tdExisting, 'title');
                    $tdDescs = array_column($tdExisting, 'description');
                }
                $tdTitles = array_merge($tdTitles ?: [], ['']);
                $tdDescs = array_merge($tdDescs ?: [], ['']);
            @endphp
            @foreach($tdTitles as $idx => $t)
                <div class="td-row flex gap-2">
                    <input type="text" name="td_titles[]" value="{{ $t }}" placeholder="Title" class="flex-1 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                    <input type="text" name="td_descriptions[]" value="{{ $tdDescs[$idx] ?? '' }}" placeholder="Description" class="flex-1 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                    <button type="button" class="remove-td text-rose-600 dark:text-rose-400 hover:text-rose-500 dark:hover:text-rose-300 text-xs">Remove</button>
                </div>
            @endforeach
        </div>
        <button type="button" id="add-td" class="text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300">+ Add item</button>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-plans">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-videos">Next</button>
    </div>
</div>

{{-- 15. Videos --}}
<div id="tab-videos" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">15. Video section (multiple)</h2>
    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Paste iframe embed code for each video from YouTube (Share → Embed).</p>
    <div id="videos-container" class="space-y-3">
        @php $videos = old('video_urls', $project->videos ?? []); @endphp
        @foreach(array_merge($videos ?: [], ['']) as $url)
            <div class="video-row flex gap-2 items-start">
                <textarea name="video_urls[]" rows="3" placeholder="Paste iframe embed code from YouTube (Share → Embed)" class="flex-1 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 font-mono placeholder-slate-400 dark:placeholder-slate-500">{{ is_array($url) ? '' : $url }}</textarea>
                <button type="button" class="remove-video text-rose-600 dark:text-rose-400 hover:text-rose-500 dark:hover:text-rose-300 text-xs py-1 px-2 rounded border border-rose-300 dark:border-rose-700 shrink-0">Remove</button>
            </div>
        @endforeach
    </div>
    <button type="button" id="add-video" class="mt-2 text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300">+ Add video</button>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-title-desc">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-gallery">Next</button>
    </div>
</div>

{{-- 16. Gallery --}}
<div id="tab-gallery" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">16. Gallery</h2>
    <p class="text-xs text-slate-500 mb-3">Add new images below. Use order number to control display order (lower = first).</p>
    <div id="gallery-remove-container" aria-hidden="true"></div>
    @if($isEdit && !empty($project->gallery))
        <div class="mb-4 space-y-2" id="gallery-existing-list">
            @foreach($project->gallery as $gidx => $g)
                <div class="gallery-item-row flex items-center gap-2 flex-wrap" data-path="{{ $g['path'] }}">
                    <img src="{{ asset('storage/' . $g['path']) }}" alt="" class="h-12 w-12 object-cover rounded" loading="lazy" decoding="async" />
                    <input type="hidden" name="gallery_paths[]" value="{{ $g['path'] }}" />
                    <input type="number" name="gallery_order[]" value="{{ $g['order'] ?? $gidx }}" min="0" class="w-16 rounded border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-2 py-1 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500" placeholder="Order" />
                    <button type="button" class="remove-gallery-item text-rose-600 dark:text-rose-400 hover:text-rose-500 dark:hover:text-rose-300 text-xs py-1 px-2 rounded border border-rose-300 dark:border-rose-700">Remove</button>
                </div>
            @endforeach
        </div>
    @endif
    <div>
        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">New images (multiple)</label>
        <input type="file" name="gallery_images[]" accept="image/*" multiple class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" />
    </div>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-videos">Back</button>
        <button type="button" class="project-tab-next inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition" data-next="tab-seo">Next</button>
    </div>
</div>

{{-- 17. SEO --}}
<div id="tab-seo" class="tab-panel rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors p-6 mb-6 hidden" role="tabpanel">
    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">17. SEO (meta tags, canonical)</h2>
    <div class="space-y-4">
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Meta title</label>
            <input type="text" name="meta_title" value="{{ old('meta_title', $project->meta_title ?? '') }}" placeholder="Optional, for &lt;title&gt; and og:title" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Meta description</label>
            <textarea name="meta_description" rows="2" placeholder="Optional, for meta description and og:description" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">{{ old('meta_description', $project->meta_description ?? '') }}</textarea>
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Meta keywords</label>
            <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $project->meta_keywords ?? '') }}" placeholder="Optional, comma-separated" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
        </div>
        <div>
            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Canonical URL</label>
            <input type="text" name="canonical_url" id="project_canonical_url" value="{{ old('canonical_url', $project->canonical_url ?? '') }}" placeholder="Optional, full URL (e.g. https://...)" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
            @error('canonical_url')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 hidden" id="project-canonical_url-error-inline" data-error-for="canonical_url"></p>
        </div>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
        <button type="button" class="project-tab-back inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" data-prev="tab-gallery">Back</button>
        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">@if($isEdit) Update project @else Create project @endif</button>
    </div>
</div>

    </div>
</div>
