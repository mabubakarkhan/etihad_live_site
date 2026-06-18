<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ $pageTitle }} | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">
                            @if(isset($filterDealer) && $filterDealer)
                                Listings: {{ $filterDealer->name }}
                            @else
                                {{ $pageTitle }}
                            @endif
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            @if(isset($filterDealer) && $filterDealer)
                                <a href="{{ route('admin.dealer-listings.index') }}" class="text-sky-600 dark:text-sky-400 hover:underline">All dealer listings</a>
                                · <a href="{{ route('admin.dealers.index') }}" class="text-slate-500 dark:text-slate-400 hover:underline">Dealers</a>
                            @else
                                Manage property listings.
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.profile.show') }}" class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">My profile</a>
                        @if(isset($filterDealer) && $filterDealer)
                            <a href="{{ route('admin.dealers.index') }}" class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Dealers</a>
                            <a href="{{ route('admin.sort-order.index', ['tab' => 'listings']) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-violet-500/50 text-violet-700 dark:text-violet-300 hover:bg-violet-500/10 transition">Sort order</a>
                            <a href="{{ route($routePrefix . '.create', ['dealer' => $filterDealer->id]) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-500 text-slate-950 hover:bg-emerald-400 transition shadow shadow-emerald-500/40">Add listing ({{ $filterDealer->name }})</a>
                        @else
                            <a href="{{ route('admin.sort-order.index', ['tab' => 'listings']) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-violet-500/50 text-violet-700 dark:text-violet-300 hover:bg-violet-500/10 transition">Sort order</a>
                            <a href="{{ route($routePrefix . '.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-500 text-slate-950 hover:bg-emerald-400 transition shadow shadow-emerald-500/40">Add listing</a>
                        @endif
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-flex">@csrf<button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Logout</button></form>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8 space-y-4">
                    @php use App\Support\PropertyEditSections; @endphp
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif
                    {{-- Filters --}}
                    @php
                        $listingIndexParams = array_filter(['dealer' => isset($filterDealer) && $filterDealer ? $filterDealer->id : null]);
                        $hasFilters = !empty($filterStatus) || !empty($filterProjectType) || !empty($filterPropertyType) || !empty($filterPurpose) || !empty($filterDhaPhase);
                    @endphp
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                        <form method="GET" action="{{ route($routePrefix . '.index', $listingIndexParams) }}" class="p-4 flex flex-wrap items-end gap-4">
                            @if(isset($filterDealer) && $filterDealer)
                                <input type="hidden" name="dealer" value="{{ $filterDealer->id }}" />
                            @endif
                            <div class="space-y-1">
                                <label for="filter-status" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Status</label>
                                <select id="filter-status" name="status" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                                    <option value="">All</option>
                                    <option value="active" {{ ($filterStatus ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="hold" {{ ($filterStatus ?? '') === 'hold' ? 'selected' : '' }}>Hold</option>
                                    <option value="inactive" {{ ($filterStatus ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="close" {{ ($filterStatus ?? '') === 'close' ? 'selected' : '' }}>Close</option>
                                </select>
                            </div>
                            @if(isset($dhaPhases) && $dhaPhases->isNotEmpty())
                            <div class="space-y-1">
                                <label for="filter-dha-phase" class="block text-xs font-medium text-slate-500 dark:text-slate-400">DHA Phase</label>
                                <select id="filter-dha-phase" name="dha_phase" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 min-w-[140px]">
                                    <option value="">All</option>
                                    @foreach($dhaPhases as $dp)
                                        <option value="{{ $dp->id }}" {{ (string)($filterDhaPhase ?? '') === (string)$dp->id ? 'selected' : '' }}>{{ $dp->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="space-y-1">
                                <label for="filter-purpose" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Purpose</label>
                                <select id="filter-purpose" name="purpose" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 min-w-[100px]">
                                    <option value="">All</option>
                                    <option value="sale" {{ ($filterPurpose ?? '') === 'sale' ? 'selected' : '' }}>Sale</option>
                                    <option value="rent" {{ ($filterPurpose ?? '') === 'rent' ? 'selected' : '' }}>Rent</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label for="filter-project-type" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Project type</label>
                                <select id="filter-project-type" name="project_type" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 min-w-[140px]">
                                    <option value="">All types</option>
                                    @foreach($projectTypes ?? [] as $pt)
                                        <option value="{{ $pt->id }}" {{ (string)($filterProjectType ?? '') === (string)$pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label for="filter-property-type" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Property type</label>
                                <select id="filter-property-type" name="property_type" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 min-w-[120px]">
                                    <option value="">All</option>
                                    <option value="plot" {{ ($filterPropertyType ?? '') === 'plot' ? 'selected' : '' }}>Plot</option>
                                    <option value="home" {{ ($filterPropertyType ?? '') === 'home' ? 'selected' : '' }}>Home</option>
                                    <option value="plaza" {{ ($filterPropertyType ?? '') === 'plaza' ? 'selected' : '' }}>Plaza</option>
                                    <option value="flat" {{ ($filterPropertyType ?? '') === 'flat' ? 'selected' : '' }}>Flat</option>
                                    <option value="apartment" {{ ($filterPropertyType ?? '') === 'apartment' ? 'selected' : '' }}>Apartment</option>
                                    <option value="file" {{ ($filterPropertyType ?? '') === 'file' ? 'selected' : '' }}>File</option>
                                </select>
                            </div>
                            <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-emerald-400 transition">Apply</button>
                            @if ($hasFilters)
                                <a href="{{ route($routePrefix . '.index', $listingIndexParams) }}" class="rounded-lg border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Clear</a>
                            @endif
                        </form>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors admin-datatable-wrapper">
                        <table class="min-w-full text-sm admin-datatable">
                            <thead class="bg-slate-100 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-xs uppercase text-slate-500 dark:text-slate-400">
                                <tr>
                                    <th class="px-4 py-2 text-left w-16">Image</th>
                                    <th class="px-4 py-2 text-left">Title</th>
                                    <th class="px-4 py-2 text-left">Dealer</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Type</th>
                                    <th class="px-4 py-2 text-left">Purpose</th>
                                    <th class="px-4 py-2 text-left">Price</th>
                                    <th class="px-4 py-2 text-left">Location</th>
                                    <th class="px-4 py-2 text-left min-w-[180px]">Sections</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse ($properties as $p)
                                    @php
                                        $baseQ = array_filter(array_merge($listingIndexParams, [
                                            'status' => $filterStatus ?? '',
                                            'project_type' => $filterProjectType ?? '',
                                            'property_type' => $filterPropertyType ?? '',
                                            'purpose' => $filterPurpose ?? '',
                                        ]));
                                    @endphp
                                    <tr class="bg-white dark:bg-slate-900/50">
                                        <td class="px-4 py-2">
                                            @if($p->featured_image)
                                                <img src="{{ asset('storage/' . $p->featured_image) }}" alt="" class="h-10 w-14 object-cover rounded border border-slate-200 dark:border-slate-700" />
                                            @else
                                                <span class="inline-flex h-10 w-14 items-center justify-center rounded bg-slate-200 dark:bg-slate-800 text-slate-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-slate-900 dark:text-slate-100 font-medium">{{ $p->title }}</td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">
                                            @if($p->dealer_id && $p->dealer)
                                                @php $dealerLinkParams = array_filter(['dealer' => $p->dealer->id, 'purpose' => $filterPurpose ?? '']); @endphp
                                                <a href="{{ route('admin.dealer-listings.index', $dealerLinkParams) }}" class="text-sky-600 dark:text-sky-400 hover:underline">{{ $p->dealer->name }}</a>
                                            @else
                                                <span class="text-slate-500 dark:text-slate-400">Own</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @php $listQ = array_filter(array_merge($baseQ, ['status' => $p->status ?? 'active'])); @endphp
                                            @if(($p->status ?? 'active') === 'active')
                                                <a href="{{ route($routePrefix . '.index', $listQ) }}" class="inline-flex rounded-full bg-emerald-500/15 px-2 py-0.5 text-[11px] font-medium text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 hover:opacity-90">Active</a>
                                            @elseif(($p->status ?? '') === 'hold')
                                                <a href="{{ route($routePrefix . '.index', $listQ) }}" class="inline-flex rounded-full bg-amber-500/15 px-2 py-0.5 text-[11px] font-medium text-amber-700 dark:text-amber-300 border border-amber-500/30 hover:opacity-90">Hold</a>
                                            @elseif(($p->status ?? '') === 'inactive')
                                                <a href="{{ route($routePrefix . '.index', $listQ) }}" class="inline-flex rounded-full bg-slate-400/20 px-2 py-0.5 text-[11px] font-medium text-slate-600 dark:text-slate-400 border border-slate-400/30 hover:opacity-90">Inactive</a>
                                            @else
                                                <a href="{{ route($routePrefix . '.index', $listQ) }}" class="inline-flex rounded-full bg-rose-500/15 px-2 py-0.5 text-[11px] font-medium text-rose-700 dark:text-rose-300 border border-rose-500/30 hover:opacity-90">Close</a>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">
                                            @if($p->projectTypes->isNotEmpty())
                                                @foreach($p->projectTypes as $pt)
                                                    @php $typeListQ = array_filter(array_merge($baseQ, ['project_type' => $pt->id])); @endphp
                                                    <a href="{{ route($routePrefix . '.index', $typeListQ) }}" class="text-sky-600 dark:text-sky-400 hover:underline">{{ $pt->name }}</a>{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            @else
                                                <span class="text-slate-500 dark:text-slate-400">—</span>
                                            @endif
                                            /
                                            @if(!empty($p->property_type))
                                                @php $propTypeListQ = array_filter(array_merge($baseQ, ['property_type' => $p->property_type])); @endphp
                                                <a href="{{ route($routePrefix . '.index', $propTypeListQ) }}" class="text-sky-600 dark:text-sky-400 hover:underline">{{ ucfirst($p->property_type) }}</a>
                                            @else
                                                <span class="text-slate-500 dark:text-slate-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">
                                            @php $purposeVal = $p->purpose ?? 'sale'; $purposeQ = array_filter(array_merge($baseQ, ['purpose' => $purposeVal])); @endphp
                                            <a href="{{ route($routePrefix . '.index', $purposeQ) }}" class="capitalize hover:underline text-sky-600 dark:text-sky-400">{{ $purposeVal }}</a>
                                        </td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ $p->price_string ?? $p->price_digits ?? '—' }}</td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">
                                            {{ $p->city ?? '—' }}, {{ $p->state ?? '—' }}
                                            @if($p->dhaPhase)
                                                <br><a href="{{ route('admin.dha-phases.edit', $p->dhaPhase) }}" class="text-violet-600 dark:text-violet-400 hover:underline text-xs">{{ $p->dhaPhase->title }}</a>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            <div class="flex flex-wrap gap-1 max-w-xs">
                                                @foreach(PropertyEditSections::all() as $slug => $meta)
                                                    <a href="{{ route($routePrefix . '.edit-section', [$p, $slug]) }}" class="text-[10px] leading-tight px-1.5 py-0.5 rounded border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">{{ $meta['label'] }}</a>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 text-left">
                                            <a href="{{ route($routePrefix . '.preview', $p) }}" target="_blank" rel="noopener noreferrer" class="text-[11px] px-2 py-1 rounded border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">View</a>
                                            <a href="{{ route('property.show', $p->slug) }}" target="_blank" rel="noopener noreferrer" class="text-[11px] px-2 py-1 rounded border border-sky-400 dark:border-sky-600 text-sky-700 dark:text-sky-300 hover:bg-sky-50 dark:hover:bg-sky-900/30 bg-sky-50/80 dark:bg-sky-900/20">Live</a>
                                            <a href="{{ route($routePrefix . '.edit', $p) }}" class="text-[11px] px-2 py-1 rounded border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Edit</a>
                                            <form method="POST" action="{{ route($routePrefix . '.duplicate', $p) }}" class="inline-block ml-1">@csrf<button type="submit" class="text-[11px] px-2 py-1 rounded border border-slate-400 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Duplicate</button></form>
                                            <form method="POST" action="{{ route($routePrefix . '.destroy', $p) }}" class="inline-block ml-1" onsubmit="return confirm('Delete this listing?');">@csrf @method('DELETE')<button type="submit" class="text-[11px] px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-300 hover:bg-rose-600/10">Delete</button></form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-empty><td colspan="10" class="px-4 py-6 text-center text-sm text-slate-500">No listings yet. <a href="{{ (isset($filterDealer) && $filterDealer) ? route($routePrefix . '.create', ['dealer' => $filterDealer->id]) : route($routePrefix . '.create') }}" class="text-emerald-600 dark:text-emerald-400">Add one</a>.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
        @include('admin.partials.datatables')
    </body>
</html>
