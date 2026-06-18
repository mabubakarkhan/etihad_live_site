@php
    $indexRoute = $source === 'project' ? 'admin.requests.projects' : 'admin.requests.properties';
@endphp
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
                            {{ $pageTitle }}
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            @if($source === 'project')
                                <a href="{{ route('admin.requests.properties') }}" class="text-sky-600 dark:text-sky-400 hover:underline">Property requests</a>
                            @else
                                <a href="{{ route('admin.requests.projects') }}" class="text-sky-600 dark:text-sky-400 hover:underline">Project requests</a>
                            @endif
                            · Form submissions from the front.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.profile.show') }}" class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">My profile</a>
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-flex">@csrf<button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Logout</button></form>
                    </div>
                </header>

                <section class="px-6 md:px-8 py-6 md:py-8 space-y-4">
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif

                    {{-- Filters --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Filters</h2>
                        </div>
                        <form method="GET" action="{{ route($indexRoute) }}" class="p-4 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                                <div class="space-y-1">
                                    <label for="status" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Status</label>
                                    <select id="status" name="status" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                                        <option value="new" {{ ($filterStatus ?? 'new') === 'new' ? 'selected' : '' }}>New</option>
                                        <option value="seen" {{ ($filterStatus ?? '') === 'seen' ? 'selected' : '' }}>Seen</option>
                                        <option value="all" {{ ($filterStatus ?? '') === 'all' ? 'selected' : '' }}>All</option>
                                    </select>
                                </div>
                                @if($source === 'property')
                                <div class="space-y-1">
                                    <label for="type" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Listing type</label>
                                    <select id="type" name="type" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                                        <option value="">All</option>
                                        <option value="own" {{ ($filterType ?? '') === 'own' ? 'selected' : '' }}>Own</option>
                                        <option value="dealer" {{ ($filterType ?? '') === 'dealer' ? 'selected' : '' }}>Dealer</option>
                                    </select>
                                </div>
                                @endif
                                <div class="space-y-1">
                                    <label for="from_date" class="block text-xs font-medium text-slate-500 dark:text-slate-400">From date</label>
                                    <input type="date" id="from_date" name="from_date" value="{{ $filterFromDate ?? '' }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                </div>
                                <div class="space-y-1">
                                    <label for="to_date" class="block text-xs font-medium text-slate-500 dark:text-slate-400">To date</label>
                                    <input type="date" id="to_date" name="to_date" value="{{ $filterToDate ?? '' }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                </div>
                                <div class="space-y-1 sm:col-span-2">
                                    <label for="search" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Search</label>
                                    <input type="text" id="search" name="search" value="{{ $filterSearch ?? '' }}" placeholder="Name, email, phone, message..." class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Apply filters</button>
                                <a href="{{ route($indexRoute) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Clear</a>
                                @if(!empty($filterStatus) || !empty($filterType) || !empty($filterFromDate) || !empty($filterToDate) || !empty($filterSearch))
                                    <span class="text-xs text-slate-500 ml-2">Filters active</span>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors admin-datatable-wrapper">
                        <table class="min-w-full text-sm admin-datatable">
                            <thead class="bg-slate-100 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-xs uppercase text-slate-500 dark:text-slate-400">
                                <tr>
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    @if($source === 'property')
                                    <th class="px-4 py-2 text-left">Type</th>
                                    <th class="px-4 py-2 text-left">Listing / Dealer</th>
                                    @else
                                    <th class="px-4 py-2 text-left">Project</th>
                                    <th class="px-4 py-2 text-left">Property type</th>
                                    <th class="px-4 py-2 text-left">Budget</th>
                                    @endif
                                    <th class="px-4 py-2 text-left">Name</th>
                                    <th class="px-4 py-2 text-left">Contact</th>
                                    <th class="px-4 py-2 text-left">Message</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse ($requests as $req)
                                <tr class="bg-white dark:bg-slate-900/50">
                                    <td class="px-4 py-2 text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $req->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium {{ $req->status === 'new' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200 border border-amber-300 dark:border-amber-700' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600' }}">
                                            {{ $req->status }}
                                        </span>
                                    </td>
                                    @if($source === 'property')
                                    <td class="px-4 py-2 text-slate-700 dark:text-slate-300">{{ $req->type === 'dealer' ? 'Dealer' : 'Own' }}</td>
                                    <td class="px-4 py-2 text-slate-700 dark:text-slate-300">
                                        @if($req->property)
                                            @php $previewRoute = $req->dealer_id > 0 ? 'admin.dealer-listings.preview' : 'admin.own-listings.preview'; @endphp
                                            <a href="{{ route($previewRoute, $req->property) }}" class="text-sky-600 dark:text-sky-400 hover:underline" target="_blank">{{ Str::limit($req->property->title, 30) }}</a>
                                            @if($req->dealer)
                                                <span class="text-slate-500"> · {{ $req->dealer->name }}</span>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    @else
                                    <td class="px-4 py-2 text-slate-700 dark:text-slate-300">
                                        @if($req->project)
                                            <a href="{{ route('admin.projects.preview', $req->project) }}" class="text-sky-600 dark:text-sky-400 hover:underline" target="_blank">{{ Str::limit($req->project->title, 30) }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ $req->property_type ?? '—' }}</td>
                                    <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ $req->budget ?? '—' }}</td>
                                    @endif
                                    <td class="px-4 py-2 text-slate-800 dark:text-slate-200 font-medium">{{ $req->name }}</td>
                                    <td class="px-4 py-2 text-slate-600 dark:text-slate-400">
                                        {{ $req->email ?? '—' }}<br><span class="text-xs">{{ $req->phone ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-slate-600 dark:text-slate-400 max-w-[200px] truncate" title="{{ $req->message }}">{{ Str::limit($req->message, 50) ?: '—' }}</td>
                                    <td class="px-4 py-2 text-left">
                                        <a href="{{ route('admin.requests.show', $req) }}" class="inline-flex items-center rounded-lg border border-slate-300 dark:border-slate-600 px-2.5 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr data-empty>
                                    <td colspan="{{ $source === 'property' ? 8 : 9 }}" class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-500">
                                        @if(!empty($filterStatus) || !empty($filterType) || !empty($filterFromDate) || !empty($filterToDate) || !empty($filterSearch))
                                            No requests matching your filters.
                                        @else
                                            No {{ $source }} requests yet.
                                        @endif
                                    </td>
                                </tr>
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
