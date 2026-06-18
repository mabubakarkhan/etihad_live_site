<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ $pageTitle }} | Etihad Admin</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        @include('admin.projects._tom_select_dark')
        @include('admin.projects._vertical_tabs_style')
        @include('admin.partials.icon_picker')
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')
            <main class="flex-1 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 overflow-auto transition-colors">
                <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">{{ $pageTitle }}</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            {{ $property->title }}
                            @if($property->dealer_id && $property->dealer)
                                · <a href="{{ route('admin.dealer-listings.index', ['dealer' => $property->dealer->id]) }}" class="text-sky-600 dark:text-sky-400 hover:underline">Dealer: {{ $property->dealer->name }}</a>
                            @else
                                · <span class="text-slate-500 dark:text-slate-400">Own</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        @if($property->dealer_id && $property->dealer)
                            <a href="{{ route('admin.dealer-listings.index', ['dealer' => $property->dealer->id]) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-sky-500/50 text-sky-600 dark:text-sky-400 hover:bg-sky-500/10 transition">Dealer listings</a>
                        @endif
                        <a href="{{ route($routePrefix . '.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Back to list</a>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8">
                    @if (session('status'))
                        <div class="mb-4 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif
                    <div id="form-success-top" class="mb-4 hidden rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-sm text-emerald-800 dark:text-emerald-200" role="status"></div>
                    <div id="form-errors-top" class="mb-4 hidden rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200" role="alert">
                        <p class="font-medium mb-1">Please fix the following:</p>
                        <ul id="form-errors-list" class="list-disc list-inside space-y-0.5"></ul>
                    </div>
                    @if ($errors->any())
                        @php
                            $listingSectionMap = [
                                'title' => ['tab' => 'tab-basic', 'name' => 'Basic'],
                                'dealer_id' => ['tab' => 'tab-basic', 'name' => 'Basic'],
                                'slug' => ['tab' => 'tab-basic', 'name' => 'Basic'],
                                'project_type_ids' => ['tab' => 'tab-basic', 'name' => 'Basic'],
                                'description' => ['tab' => 'tab-basic', 'name' => 'Basic'],
                                'status' => ['tab' => 'tab-status', 'name' => 'Status'],
                                'featured_image' => ['tab' => 'tab-featured-image', 'name' => 'Featured image'],
                                'featured_image_path' => ['tab' => 'tab-featured-image', 'name' => 'Featured image'],
                                'state' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'city' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'address' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'short_address' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'town' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'latitude' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'longitude' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'google_map' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'videos' => ['tab' => 'tab-videos', 'name' => 'Video'],
                                'price_string' => ['tab' => 'tab-price', 'name' => 'Price'],
                                'price_digits' => ['tab' => 'tab-price', 'name' => 'Price'],
                                'property_type' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
                                'bedrooms' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
                                'bathrooms' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
                                'garage' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
                                'kitchen' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
                                'area_marla' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
                                'area_kanal' => ['tab' => 'tab-property-type', 'name' => 'Property type & area'],
                                'meta_title' => ['tab' => 'tab-seo', 'name' => 'SEO'],
                                'meta_description' => ['tab' => 'tab-seo', 'name' => 'SEO'],
                                'meta_keywords' => ['tab' => 'tab-seo', 'name' => 'SEO'],
                                'canonical_url' => ['tab' => 'tab-seo', 'name' => 'SEO'],
                                'amenities_description' => ['tab' => 'tab-amenities', 'name' => 'Amenities'],
                            ];
                        @endphp
                        <div class="mb-4 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200" role="alert">
                            <p class="font-medium mb-1">Please fix the following:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->getBag('default')->getMessages() as $field => $messages)
                                    @php $section = $listingSectionMap[$field] ?? ['tab' => 'tab-basic', 'name' => 'Basic']; @endphp
                                    @foreach($messages as $msg)
                                        <li>{{ $msg }} <span class="text-rose-700 dark:text-rose-300 font-medium">(Section: {{ $section['name'] }})</span>
                                            <button type="button" class="go-to-tab ml-1 text-xs underline hover:no-underline font-semibold" data-tab="{{ $section['tab'] }}">Go to: {{ $section['name'] }}</button>
                                        </li>
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route($routePrefix . '.update', $property) }}" id="property-form"
                        data-upload-url="{{ route($routePrefix . '.upload-media') }}"
                        data-property-id="{{ $property->id }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-6 flex flex-wrap items-center gap-3">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Update listing</button>
                            <a href="{{ route($routePrefix . '.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 dark:border-slate-600 px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Cancel</a>
                        </div>
                        @include('admin.properties._form')
                    </form>
                </section>
            </main>
        </div>
        <script src="{{ asset('theme/js/admin-property-media.js') }}"></script>
        @include('admin.properties._form_scripts')
    </body>
</html>
