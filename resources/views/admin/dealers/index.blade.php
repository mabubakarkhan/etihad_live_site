<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Dealers | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Dealers</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage dealers for property listings.</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.profile.show') }}" class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">My profile</a>
                        <a href="{{ route('admin.dealers.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-500 text-slate-950 hover:bg-emerald-400 transition shadow shadow-emerald-500/40">Add dealer</a>
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-flex">@csrf<button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Logout</button></form>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8 space-y-4">
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif
                    {{-- Filters --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 overflow-hidden transition-colors">
                        <form method="GET" action="{{ route('admin.dealers.index') }}" class="p-4 flex flex-wrap items-end gap-4">
                            <div class="space-y-1">
                                <label for="filter-status" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Status</label>
                                <select id="filter-status" name="status" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                                    <option value="">All</option>
                                    <option value="active" {{ ($filterStatus ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ ($filterStatus ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label for="filter-show-homepage" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Show Homepage</label>
                                <select id="filter-show-homepage" name="show_homepage" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                                    <option value="">All</option>
                                    <option value="yes" {{ ($filterShowHomepage ?? '') === 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ ($filterShowHomepage ?? '') === 'no' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label for="filter-show-homepage-ad" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Homepage Ad</label>
                                <select id="filter-show-homepage-ad" name="show_homepage_ad" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                                    <option value="">All</option>
                                    <option value="yes" {{ ($filterShowHomepageAd ?? '') === 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ ($filterShowHomepageAd ?? '') === 'no' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-emerald-400 transition">Apply</button>
                            @if (!empty($filterStatus) || !empty($filterShowHomepage) || !empty($filterShowHomepageAd))
                                <a href="{{ route('admin.dealers.index') }}" class="rounded-lg border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Clear</a>
                            @endif
                        </form>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 overflow-hidden transition-colors admin-datatable-wrapper">
                        <table class="min-w-full text-sm admin-datatable">
                            <thead class="bg-slate-100 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-xs uppercase text-slate-500 dark:text-slate-400">
                                <tr>
                                    <th class="px-4 py-2 text-left w-14">Photo</th>
                                    <th class="px-4 py-2 text-left">Name</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Homepage</th>
                                    <th class="px-4 py-2 text-left">Homepage Ad</th>
                                    <th class="px-4 py-2 text-left">Email / Phone</th>
                                    <th class="px-4 py-2 text-left">City / State</th>
                                    <th class="px-4 py-2 text-left">Properties</th>
                                    <th class="px-4 py-2 text-left">Views</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse ($dealers as $dealer)
                                    <tr class="bg-white dark:bg-slate-900/50">
                                        <td class="px-4 py-2">
                                            @if($dealer->profile_pic)
                                                <img src="{{ asset('storage/' . $dealer->profile_pic) }}" alt="" class="h-10 w-10 rounded-full object-cover border border-slate-200 dark:border-slate-700" />
                                            @else
                                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-800 text-slate-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-slate-900 dark:text-slate-100 font-medium">{{ $dealer->name }}</td>
                                        <td class="px-4 py-2">
                                            @if(($dealer->status ?? 'active') === 'active')
                                                <a href="{{ route('admin.dealers.index', ['status' => 'active']) }}" class="inline-flex items-center rounded-full bg-emerald-500/15 px-2 py-0.5 text-[11px] font-medium text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 hover:opacity-90">Active</a>
                                            @else
                                                <a href="{{ route('admin.dealers.index', ['status' => 'inactive']) }}" class="inline-flex items-center rounded-full bg-slate-400/20 px-2 py-0.5 text-[11px] font-medium text-slate-600 dark:text-slate-400 border border-slate-400/30 hover:opacity-90">Inactive</a>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($dealer->show_homepage)
                                                <a href="{{ route('admin.dealers.index', ['show_homepage' => 'yes']) }}" class="inline-flex items-center rounded-full bg-emerald-500/15 px-2 py-0.5 text-[11px] font-medium text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 hover:opacity-90">Yes</a>
                                            @else
                                                <a href="{{ route('admin.dealers.index', ['show_homepage' => 'no']) }}" class="inline-flex items-center rounded-full bg-slate-400/20 px-2 py-0.5 text-[11px] font-medium text-slate-600 dark:text-slate-400 border border-slate-400/30 hover:opacity-90">No</a>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($dealer->show_homepage_ad)
                                                <a href="{{ route('admin.dealers.index', ['show_homepage_ad' => 'yes']) }}" class="inline-flex items-center rounded-full bg-sky-500/15 px-2 py-0.5 text-[11px] font-medium text-sky-700 dark:text-sky-300 border border-sky-500/30 hover:opacity-90">Yes</a>
                                            @else
                                                <a href="{{ route('admin.dealers.index', ['show_homepage_ad' => 'no']) }}" class="inline-flex items-center rounded-full bg-slate-400/20 px-2 py-0.5 text-[11px] font-medium text-slate-600 dark:text-slate-400 border border-slate-400/30 hover:opacity-90">No</a>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ $dealer->email ?? '—' }}<br><span class="text-xs">{{ $dealer->phone ?? $dealer->mobile ?? '' }}</span></td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ $dealer->city ?? '—' }} / {{ $dealer->state ?? '—' }}</td>
                                        <td class="px-4 py-2"><span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2 py-0.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ $dealer->properties_count }}</span></td>
                                        <td class="px-4 py-2"><span class="inline-flex items-center rounded-full bg-slate-500/10 px-2 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">{{ number_format((int) ($dealer->view_count ?? 0)) }}</span></td>
                                        <td class="px-4 py-2 text-left">
                                            <a href="{{ route('admin.dealer-listings.index', ['dealer' => $dealer->id]) }}" class="text-[11px] px-2 py-1 rounded border border-sky-500/50 text-sky-600 dark:text-sky-400 hover:bg-sky-500/10">Listings</a>
                                            <a href="{{ route('admin.dealers.edit', $dealer) }}" class="text-[11px] px-2 py-1 rounded border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Edit</a>
                                            <form method="POST" action="{{ route('admin.dealers.destroy', $dealer) }}" class="inline-block ml-1" onsubmit="return confirm('Delete this dealer? Their listings will become own listings.');">@csrf @method('DELETE')<button type="submit" class="text-[11px] px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-300 hover:bg-rose-600/10">Delete</button></form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-empty><td colspan="10" class="px-4 py-6 text-center text-sm text-slate-500">No dealers yet. <a href="{{ route('admin.dealers.create') }}" class="text-emerald-600 dark:text-emerald-400">Add dealer</a>.</td></tr>
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
