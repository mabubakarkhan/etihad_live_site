@php
    $td = $project->title_descriptions ?? [];
    $tdItems = $td['items'] ?? [];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View: {{ $project->title }} | Etihad Admin</title>
    @include('admin.partials.theme-init')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
    <div class="min-h-screen flex">
        @include('admin.partials.sidebar')
        <main class="flex-1 overflow-auto">
            <header class="sticky top-0 z-10 px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-100/95 dark:bg-slate-950/95 flex items-center justify-between flex-wrap gap-3">
                <h1 class="text-lg font-semibold text-slate-900 dark:text-slate-50">{{ $project->title }}</h1>
                <div class="flex items-center gap-2">
                    @include('admin.partials.theme-toggle')
                    <a href="{{ route('admin.projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-emerald-500 text-slate-950 hover:bg-emerald-400">Edit</a>
                    <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800">Close</a>
                </div>
            </header>
            <div class="p-6 space-y-6 max-w-4xl">
                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Basics</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <dt class="text-slate-500 dark:text-slate-400">Types</dt><dd>{{ $project->projectTypes->pluck('name')->join(', ') ?: '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Title</dt><dd class="font-medium">{{ $project->title ?: '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Slug</dt><dd class="font-mono text-xs">{{ $project->slug ?: '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Price</dt><dd>{{ $project->price ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400 md:col-span-2">Description</dt><dd class="md:col-span-2 prose prose-sm dark:prose-invert max-w-none">{!! $project->description ?: '—' !!}</dd>
                    </dl>
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Address</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <dt class="text-slate-500 dark:text-slate-400">State</dt><dd>{{ $project->state ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">City</dt><dd>{{ $project->city ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Short address</dt><dd>{{ $project->short_address ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400 md:col-span-2">Full address</dt><dd class="md:col-span-2">{{ $project->full_address ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Lat / Long</dt><dd>{{ ($project->latitude && $project->longitude) ? $project->latitude . ', ' . $project->longitude : '—' }}</dd>
                        @if($project->address_image)
                            <dt class="text-slate-500 dark:text-slate-400">Address image</dt>
                            <dd><img src="{{ asset('storage/' . $project->address_image) }}" alt="" class="max-h-40 rounded-lg border border-slate-200 dark:border-slate-700" loading="lazy" /></dd>
                        @endif
                    </dl>
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Project media</h2>
                    <div class="flex flex-wrap gap-4">
                        @if($project->logo)
                            <div class="flex flex-col">
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Logo</p>
                                <div class="h-24 w-24 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $project->logo) }}" alt="Logo" class="h-24 w-24 object-cover" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling && (this.nextElementSibling.style.display='block');" />
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 px-1 text-center" style="display:none;">Unavailable</span>
                                </div>
                            </div>
                        @endif
                        @if($project->featured_image)
                            <div class="flex flex-col">
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Featured</p>
                                <div class="h-24 w-24 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $project->featured_image) }}" alt="Featured" class="h-24 w-24 object-cover" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling && (this.nextElementSibling.style.display='block');" />
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 px-1 text-center" style="display:none;">Unavailable</span>
                                </div>
                            </div>
                        @endif
                        @if($project->homepage_listing_image)
                            <div class="flex flex-col">
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Homepage</p>
                                <div class="h-24 w-24 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $project->homepage_listing_image) }}" alt="Homepage" class="h-24 w-24 object-cover" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling && (this.nextElementSibling.style.display='block');" />
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 px-1 text-center" style="display:none;">Unavailable</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if(!$project->logo && !$project->featured_image && !$project->homepage_listing_image)<p class="text-sm text-slate-500">No images.</p>@endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Featured video</h2>
                    @if($project->featured_video_title)<p class="font-medium text-sm mb-1">{{ $project->featured_video_title }}</p>@endif
                    @if($project->featured_youtube_url)<div class="prose prose-sm dark:prose-invert max-w-none mb-2">{!! $project->featured_youtube_url !!}</div>@endif
                    @if($project->featured_video_description)<div class="prose prose-sm dark:prose-invert max-w-none">{!! $project->featured_video_description !!}</div>@endif
                    @if(!$project->featured_youtube_url && !$project->featured_video_title && !$project->featured_video_description)<p class="text-sm text-slate-500">No featured video.</p>@endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">About developers</h2>
                    @if($project->developer_logo)
                        <div class="mb-3"><img src="{{ asset('storage/' . $project->developer_logo) }}" alt="Developer logo" class="max-h-20 object-contain rounded border border-slate-200 dark:border-slate-600" loading="lazy" /></div>
                    @endif
                    <div class="prose prose-sm dark:prose-invert max-w-none">{{ $project->about_developers ? nl2br(e($project->about_developers)) : '—' }}</div>
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Project file (PDF)</h2>
                    @if($project->project_file_pdf)<a href="{{ asset('storage/' . $project->project_file_pdf) }}" target="_blank" class="text-emerald-600 dark:text-emerald-400 hover:underline">Open PDF</a>@else<p class="text-sm text-slate-500">No PDF.</p>@endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">NOC & planning</h2>
                    @if($project->noc_planning_content)<div class="prose prose-sm dark:prose-invert max-w-none mb-2">{!! $project->noc_planning_content !!}</div>@endif
                    @if($project->noc_planning_image)<img src="{{ asset('storage/' . $project->noc_planning_image) }}" alt="NOC" class="max-h-48 rounded-lg border" loading="lazy" />@endif
                    @if(!$project->noc_planning_content && !$project->noc_planning_image)<p class="text-sm text-slate-500">No content.</p>@endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Future note</h2>
                    @if($project->future_note_title)<p class="font-medium">{{ $project->future_note_title }}</p>@endif
                    <div class="prose prose-sm dark:prose-invert max-w-none">{{ $project->future_note_content ? nl2br(e($project->future_note_content)) : '—' }}</div>
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Extra section</h2>
                    @if($project->extra_section_title)<p class="font-medium">{{ $project->extra_section_title }}</p>@endif
                    <div class="prose prose-sm dark:prose-invert max-w-none">{!! $project->extra_section_content ?? '—' !!}</div>
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Unique features</h2>
                    @php $features = $project->unique_features ?? []; @endphp
                    @if(!empty($features))<ul class="space-y-1 text-sm">@foreach($features as $f)<li>{{ $f['title'] ?? '' }} @if(!empty($f['icon']))<span class="text-slate-400">({{ $f['icon'] }})</span>@endif</li>@endforeach</ul>@else<p class="text-sm text-slate-500">None.</p>@endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Price plan</h2>
                    @if($project->price_plan_section_title)<p class="font-medium mb-2">{{ $project->price_plan_section_title }}</p>@endif
                    @php $items = $project->price_plan_items ?? []; @endphp
                    @if(!empty($items))<ul class="list-disc list-inside text-sm space-y-1">@foreach($items as $i)<li>{{ $i }}</li>@endforeach</ul>@else<p class="text-sm text-slate-500">No items.</p>@endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">FAQs</h2>
                    @php $faqs = $project->faqs ?? []; @endphp
                    @if(!empty($faqs))<dl class="space-y-3 text-sm">@foreach($faqs as $faq)<div><dt class="font-medium">{{ $faq['question'] ?? '' }}</dt><dd class="text-slate-600 dark:text-slate-400 mt-0.5">{{ $faq['answer'] ?? '' }}</dd></div>@endforeach</dl>@else<p class="text-sm text-slate-500">None.</p>@endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Plans</h2>
                    @php $plans = $project->plans ?? []; @endphp
                    @if(!empty($plans))<div class="flex flex-wrap gap-4">@foreach($plans as $p)<div class="text-center">@if(!empty($p['image']))<img src="{{ asset('storage/' . $p['image']) }}" alt="" class="h-24 w-24 object-cover rounded-lg border mx-auto mb-1" loading="lazy" />@endif<p class="text-xs font-medium">{{ $p['title'] ?? '' }}</p></div>@endforeach</div>@else<p class="text-sm text-slate-500">None.</p>@endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Title + descriptions</h2>
                    @if(!empty($td['section_title']))<p class="font-medium">{{ $td['section_title'] }}</p>@endif
                    @if(!empty($td['section_description']))<p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ $td['section_description'] }}</p>@endif
                    @if(!empty($tdItems))<dl class="space-y-2 text-sm">@foreach($tdItems as $item)<div><dt class="font-medium">{{ $item['title'] ?? '' }}</dt><dd class="text-slate-600 dark:text-slate-400">{{ $item['description'] ?? '' }}</dd></div>@endforeach</dl>@endif
                    @if(empty($td['section_title']) && empty($tdItems))<p class="text-sm text-slate-500">None.</p>@endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Videos</h2>
                    @php $videos = $project->videos ?? []; @endphp
                    @if(!empty($videos))
                        <div class="space-y-3">
                            @foreach($videos as $v)
                                @if($v)<div class="prose prose-sm dark:prose-invert max-w-none">{!! $v !!}</div>@endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500">None.</p>
                    @endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Gallery</h2>
                    @php $gallery = collect($project->gallery ?? [])->sortBy('order'); @endphp
                    @if($gallery->isNotEmpty())<div class="flex flex-wrap gap-2">@foreach($gallery as $g)<img src="{{ asset('storage/' . ($g['path'] ?? '')) }}" alt="" class="h-20 w-20 object-cover rounded-lg border" loading="lazy" />@endforeach</div>@else<p class="text-sm text-slate-500">No images.</p>@endif
                </section>

                @if($project->meta_title || $project->meta_description || $project->meta_keywords || $project->canonical_url)
                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">SEO</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <dt class="text-slate-500 dark:text-slate-400">Meta title</dt><dd>{{ $project->meta_title ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400 md:col-span-2">Meta description</dt><dd class="md:col-span-2">{{ $project->meta_description ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Meta keywords</dt><dd>{{ $project->meta_keywords ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Canonical URL</dt><dd class="break-all">{{ $project->canonical_url ?? '—' }}</dd>
                    </dl>
                </section>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
