<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View: {{ $property->title }} | Etihad Admin</title>
    @include('admin.partials.theme-init')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
    <div class="min-h-screen flex">
        @include('admin.partials.sidebar')
        <main class="flex-1 overflow-auto">
            <header class="sticky top-0 z-10 px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-100/95 dark:bg-slate-950/95 flex items-center justify-between flex-wrap gap-3">
                <h1 class="text-lg font-semibold text-slate-900 dark:text-slate-50">{{ $property->title }}</h1>
                <div class="flex items-center gap-2">
                    @include('admin.partials.theme-toggle')
                    @if($property->dealer_id && $property->dealer)
                        <a href="{{ route('admin.dealer-listings.index', ['dealer' => $property->dealer->id]) }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border border-sky-500/50 text-sky-600 dark:text-sky-400 hover:bg-sky-500/10">Dealer listings</a>
                    @endif
                    <a href="{{ route($routePrefix . '.edit', $property) }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-emerald-500 text-slate-950 hover:bg-emerald-400">Edit</a>
                    <a href="{{ route($routePrefix . '.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800">Close</a>
                </div>
            </header>
            <div class="p-6 space-y-6 max-w-4xl">
                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Basic</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <dt class="text-slate-500 dark:text-slate-400">Title</dt><dd class="font-medium">{{ $property->title ?: '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Slug</dt><dd class="font-mono text-xs">{{ $property->slug ?: '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Project types</dt><dd>{{ $property->projectTypes->isNotEmpty() ? $property->projectTypes->pluck('name')->join(', ') : '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Purpose</dt><dd class="capitalize"><a href="{{ route($routePrefix . '.index', ['purpose' => $property->purpose ?? 'sale']) }}" class="text-sky-600 dark:text-sky-400 hover:underline">{{ $property->purpose ?? 'sale' }}</a></dd>
                        <dt class="text-slate-500 dark:text-slate-400">Dealer</dt><dd>@if($property->dealer_id && $property->dealer)<a href="{{ route('admin.dealer-listings.index', ['dealer' => $property->dealer->id]) }}" class="text-sky-600 dark:text-sky-400 hover:underline">{{ $property->dealer->name }}</a>@else<span class="text-slate-500 dark:text-slate-400">Own</span>@endif</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Status</dt><dd>@php $st = $property->status ?? 'active'; @endphp @if($st === 'active')<span class="inline-flex rounded-full bg-emerald-500/15 px-2 py-0.5 text-[11px] font-medium text-emerald-700 dark:text-emerald-300">Active</span>@elseif($st === 'hold')<span class="inline-flex rounded-full bg-amber-500/15 px-2 py-0.5 text-[11px] font-medium text-amber-700 dark:text-amber-300">Hold</span>@elseif($st === 'inactive')<span class="inline-flex rounded-full bg-slate-400/20 px-2 py-0.5 text-[11px] font-medium text-slate-600 dark:text-slate-400">Inactive</span>@else<span class="inline-flex rounded-full bg-rose-500/15 px-2 py-0.5 text-[11px] font-medium text-rose-700 dark:text-rose-300">Close</span>@endif</dd>
                        <dt class="text-slate-500 dark:text-slate-400 md:col-span-2">Description</dt><dd class="md:col-span-2 prose prose-sm dark:prose-invert max-w-none">{!! $property->description ?: '—' !!}</dd>
                    </dl>
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Featured image</h2>
                    @if($property->featured_image)
                        <img src="{{ asset('storage/' . $property->featured_image) }}" alt="" class="max-h-48 rounded-lg border border-slate-200 dark:border-slate-700" loading="lazy" />
                    @else
                        <p class="text-sm text-slate-500">No image.</p>
                    @endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Address</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <dt class="text-slate-500 dark:text-slate-400">State</dt><dd>{{ $property->state ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">City</dt><dd>{{ $property->city ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Town</dt><dd>{{ $property->town ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Short address</dt><dd>{{ $property->short_address ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400 md:col-span-2">Address</dt><dd class="md:col-span-2">{{ $property->address ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Lat / Long</dt><dd>{{ ($property->latitude && $property->longitude) ? $property->latitude . ', ' . $property->longitude : '—' }}</dd>
                    </dl>
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Video</h2>
                    @php $videos = $property->videos ?? []; @endphp
                    @if(!empty($videos))
                        <div class="space-y-2">@foreach($videos as $v) @if($v)<div class="prose prose-sm dark:prose-invert max-w-none">{!! $v !!}</div>@endif @endforeach</div>
                    @else
                        <p class="text-sm text-slate-500">None.</p>
                    @endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Price</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <dt class="text-slate-500 dark:text-slate-400">Price (string)</dt><dd>{{ $property->price_string ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Price (digits)</dt><dd>{{ $property->price_digits ?? '—' }}</dd>
                    </dl>
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Property type & area</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <dt class="text-slate-500 dark:text-slate-400">Property type</dt><dd>{{ $property->property_type ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Marla</dt><dd>{{ $property->area_marla ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Kanal</dt><dd>{{ $property->area_kanal ?? '—' }}</dd>
                        @if(in_array($property->property_type, ['home', 'flat']))
                            <dt class="text-slate-500 dark:text-slate-400">Bedrooms</dt><dd>{{ $property->bedrooms ?? '—' }}</dd>
                            <dt class="text-slate-500 dark:text-slate-400">Bathrooms</dt><dd>{{ $property->bathrooms ?? '—' }}</dd>
                            <dt class="text-slate-500 dark:text-slate-400">Garage</dt><dd>{{ $property->garage ?? '—' }}</dd>
                            <dt class="text-slate-500 dark:text-slate-400">Kitchen</dt><dd>{{ $property->kitchen ?? '—' }}</dd>
                        @endif
                    </dl>
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Features & nearby</h2>
                    @foreach(['features' => 'Features', 'location_accessibility' => 'Location accessibility', 'nearest_hospitals' => 'Nearest hospitals', 'nearest_markets' => 'Nearest markets', 'nearest_restaurants' => 'Nearest restaurants / cafes / bakeries'] as $key => $label)
                        @php $arr = $property->$key ?? []; @endphp
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-2">{{ $label }}</p>
                        @if(!empty($arr))<ul class="list-disc list-inside text-sm space-y-0.5">@foreach($arr as $v)<li>{{ $v }}</li>@endforeach</ul>@else<p class="text-sm text-slate-500">—</p>@endif
                    @endforeach
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Amenities</h2>
                    @if($property->amenities_description)<div class="prose prose-sm dark:prose-invert max-w-none mb-2">{{ $property->amenities_description }}</div>@endif
                    @php $amenities = $property->amenities ?? []; @endphp
                    @if(!empty($amenities))<ul class="space-y-1 text-sm">@foreach($amenities as $a)<li>{{ $a['title'] ?? '' }} @if(!empty($a['icon']))<span class="text-slate-400">({{ $a['icon'] }})</span>@endif</li>@endforeach</ul>@else<p class="text-sm text-slate-500">None.</p>@endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Image gallery</h2>
                    @php $gallery = collect($property->gallery ?? [])->sortBy('order'); @endphp
                    @if($gallery->isNotEmpty())<div class="flex flex-wrap gap-2">@foreach($gallery as $g)<img src="{{ asset('storage/' . ($g['path'] ?? '')) }}" alt="" class="h-20 w-20 object-cover rounded-lg border" loading="lazy" />@endforeach</div>@else<p class="text-sm text-slate-500">No images.</p>@endif
                </section>

                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">Video gallery</h2>
                    @php $vg = $property->video_gallery ?? []; @endphp
                    @if(!empty($vg))<div class="space-y-2">@foreach($vg as $v) @if($v)<div class="prose prose-sm dark:prose-invert max-w-none">{!! $v !!}</div>@endif @endforeach</div>@else<p class="text-sm text-slate-500">None.</p>@endif
                </section>

                @if($property->meta_title || $property->meta_description || $property->meta_keywords || $property->canonical_url)
                <section class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                    <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">SEO</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <dt class="text-slate-500 dark:text-slate-400">Meta title</dt><dd>{{ $property->meta_title ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400 md:col-span-2">Meta description</dt><dd class="md:col-span-2">{{ $property->meta_description ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Meta keywords</dt><dd>{{ $property->meta_keywords ?? '—' }}</dd>
                        <dt class="text-slate-500 dark:text-slate-400">Canonical URL</dt><dd class="break-all">{{ $property->canonical_url ?? '—' }}</dd>
                    </dl>
                </section>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
