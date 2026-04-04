<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Reports | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Reports</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Analytics and breakdowns by date range.</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.profile.show') }}" class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">My profile</a>
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-flex">@csrf<button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Logout</button></form>
                    </div>
                </header>

                <section class="px-6 md:px-8 py-6 md:py-8 space-y-6">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                        <form method="GET" action="{{ route('admin.reports.index') }}" class="p-4 flex flex-wrap items-end gap-4">
                            <div class="space-y-1">
                                <label for="from_date" class="block text-xs font-medium text-slate-500 dark:text-slate-400">From date</label>
                                <input type="date" id="from_date" name="from_date" value="{{ $fromStr }}" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                            </div>
                            <div class="space-y-1">
                                <label for="to_date" class="block text-xs font-medium text-slate-500 dark:text-slate-400">To date</label>
                                <input type="date" id="to_date" name="to_date" value="{{ $toStr }}" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100" />
                            </div>
                            <div class="space-y-1">
                                <label for="filter-purpose" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Listings purpose</label>
                                <select id="filter-purpose" name="purpose" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 min-w-[100px]">
                                    <option value="">All</option>
                                    <option value="sale" {{ ($filterPurpose ?? '') === 'sale' ? 'selected' : '' }}>Sale</option>
                                    <option value="rent" {{ ($filterPurpose ?? '') === 'rent' ? 'selected' : '' }}>Rent</option>
                                </select>
                            </div>
                            <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-emerald-400 transition">Apply</button>
                        </form>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/70 p-4 shadow-lg transition-colors">
                            <p class="text-xs font-medium tracking-[0.18em] uppercase text-slate-500 dark:text-slate-500">Projects (range)</p>
                            <p class="text-2xl font-semibold text-slate-900 dark:text-slate-50 mt-1">{{ number_format($projectsInRange) }}</p>
                            <p class="text-xs text-slate-500 mt-1">of {{ number_format($projectsTotal) }} total</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/70 p-4 shadow-lg transition-colors">
                            <p class="text-xs font-medium tracking-[0.18em] uppercase text-slate-500 dark:text-slate-500">Listings (range)</p>
                            <p class="text-2xl font-semibold text-slate-900 dark:text-slate-50 mt-1">{{ number_format($listingsInRange) }}</p>
                            <p class="text-xs text-slate-500 mt-1">of {{ number_format($listingsTotal) }} total</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/70 p-4 shadow-lg transition-colors">
                            <p class="text-xs font-medium tracking-[0.18em] uppercase text-slate-500 dark:text-slate-500">Dealers (range)</p>
                            <p class="text-2xl font-semibold text-slate-900 dark:text-slate-50 mt-1">{{ number_format($dealersInRange) }}</p>
                            <p class="text-xs text-slate-500 mt-1">of {{ number_format($dealersTotal) }} total</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/70 p-4 shadow-lg transition-colors">
                            <p class="text-xs font-medium tracking-[0.18em] uppercase text-slate-500 dark:text-slate-500">Activity (range)</p>
                            <p class="text-2xl font-semibold text-slate-900 dark:text-slate-50 mt-1">{{ number_format($activityInRange) }}</p>
                            <p class="text-xs text-slate-500 mt-1">log entries</p>
                        </div>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Projects by status</p>
                            </div>
                            <div class="p-4">
                                <table class="min-w-full text-sm">
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                        @foreach(['active'=>'Active','hold'=>'Hold','inactive'=>'Inactive','close'=>'Close'] as $k => $label)
                                            <tr>
                                                <td class="py-2 text-slate-700 dark:text-slate-300">{{ $label }}</td>
                                                <td class="py-2 text-right font-medium">{{ $projectsByStatus[$k] ?? 0 }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Projects by type</p>
                            </div>
                            <div class="p-4 max-h-48 overflow-y-auto">
                                @forelse($projectsByType as $row)
                                    <div class="flex justify-between py-1.5 text-sm"><span class="text-slate-700 dark:text-slate-300">{{ $row->name }}</span><span class="font-medium">{{ $row->cnt }}</span></div>
                                @empty
                                    <p class="text-sm text-slate-500">No data</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Listings by status</p>
                            </div>
                            <div class="p-4">
                                <table class="min-w-full text-sm">
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                        @foreach(['active'=>'Active','hold'=>'Hold','inactive'=>'Inactive','close'=>'Close'] as $k => $label)
                                            <tr>
                                                <td class="py-2 text-slate-700 dark:text-slate-300">{{ $label }}</td>
                                                <td class="py-2 text-right font-medium">{{ $listingsByStatus[$k] ?? 0 }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <p class="text-xs text-slate-500 mt-2">Own: {{ $ownListings }} · Dealer: {{ $dealerListings }}</p>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Listings by purpose</p>
                            </div>
                            <div class="p-4">
                                <table class="min-w-full text-sm">
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                        <tr>
                                            <td class="py-2 text-slate-700 dark:text-slate-300">Sale</td>
                                            <td class="py-2 text-right font-medium">{{ $listingsByPurpose['sale'] ?? 0 }}</td>
                                            <td class="py-2 text-right">
                                                <a href="{{ route('admin.own-listings.index', ['purpose' => 'sale']) }}" class="text-[11px] text-sky-600 dark:text-sky-400 hover:underline">Own</a>
                                                <a href="{{ route('admin.dealer-listings.index', ['purpose' => 'sale']) }}" class="text-[11px] text-sky-600 dark:text-sky-400 hover:underline ml-2">Dealer</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 text-slate-700 dark:text-slate-300">Rent</td>
                                            <td class="py-2 text-right font-medium">{{ $listingsByPurpose['rent'] ?? 0 }}</td>
                                            <td class="py-2 text-right">
                                                <a href="{{ route('admin.own-listings.index', ['purpose' => 'rent']) }}" class="text-[11px] text-sky-600 dark:text-sky-400 hover:underline">Own</a>
                                                <a href="{{ route('admin.dealer-listings.index', ['purpose' => 'rent']) }}" class="text-[11px] text-sky-600 dark:text-sky-400 hover:underline ml-2">Dealer</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Listings by property type</p>
                            </div>
                            <div class="p-4 max-h-48 overflow-y-auto">
                                @forelse($listingsByPropertyType as $row)
                                    <div class="flex justify-between py-1.5 text-sm"><span class="text-slate-700 dark:text-slate-300 capitalize">{{ $row->property_type }}</span><span class="font-medium">{{ $row->cnt }}</span></div>
                                @empty
                                    <p class="text-sm text-slate-500">No data</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Listings by project type</p>
                            </div>
                            <div class="p-4 max-h-48 overflow-y-auto">
                                @forelse($listingsByProjectType as $row)
                                    <div class="flex justify-between py-1.5 text-sm"><span class="text-slate-700 dark:text-slate-300">{{ $row->name }}</span><span class="font-medium">{{ $row->cnt }}</span></div>
                                @empty
                                    <p class="text-sm text-slate-500">No data</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Dealers by status</p>
                            </div>
                            <div class="p-4">
                                <table class="min-w-full text-sm">
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                        <tr><td class="py-2 text-slate-700 dark:text-slate-300">Active</td><td class="py-2 text-right font-medium">{{ $dealersByStatus['active'] ?? $dealersActive }}</td></tr>
                                        <tr><td class="py-2 text-slate-700 dark:text-slate-300">Inactive</td><td class="py-2 text-right font-medium">{{ $dealersByStatus['inactive'] ?? 0 }}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Activity by action (in range)</p>
                        </div>
                        <div class="p-4">
                            <div class="flex flex-wrap gap-3">
                                @forelse($activityByAction as $row)
                                    <span class="inline-flex items-center rounded-full bg-slate-200 dark:bg-slate-800 px-3 py-1 text-xs font-medium text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700">{{ $row->action }}: {{ $row->cnt }}</span>
                                @empty
                                    <p class="text-sm text-slate-500">No activity in range</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Top users by activity (in range)</p>
                        </div>
                        <div class="p-4 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-xs uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                                    <tr><th class="text-left py-2 pr-4">User</th><th class="text-right py-2">Actions</th></tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                    @forelse($topUsersByActivity as $row)
                                        <tr>
                                            <td class="py-2 text-slate-700 dark:text-slate-300">{{ $row->user ? $row->user->username : '—' }}</td>
                                            <td class="py-2 text-right font-medium">{{ $row->cnt }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="py-4 text-center text-slate-500">No activity in range</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @php
                        $allDays = [];
                        $start = \Carbon\Carbon::parse($fromStr);
                        $end = \Carbon\Carbon::parse($toStr);
                        for ($d = $start->copy(); $d->lte($end); $d->addDay()) { $allDays[] = $d->format('Y-m-d'); }
                        $maxProjects = !empty($dailyProjects) ? max($dailyProjects) : 1;
                        $maxListings = !empty($dailyListings) ? max($dailyListings) : 1;
                        $maxDealers = !empty($dailyDealers) ? max($dailyDealers) : 1;
                    @endphp
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Daily trend (in range)</p>
                        </div>
                        <div class="p-4 overflow-x-auto">
                            <div class="flex gap-1 items-end min-w-max" style="min-height: 120px;">
                                @foreach(array_slice($allDays, -21) as $d)
                                    @php
                                        $p = $dailyProjects[$d] ?? 0;
                                        $l = $dailyListings[$d] ?? 0;
                                        $dr = $dailyDealers[$d] ?? 0;
                                    @endphp
                                    <div class="flex flex-col items-center gap-1" title="{{ $d }}: P{{ $p }} L{{ $l }} D{{ $dr }}">
                                        <div class="flex gap-0.5 items-end h-20">
                                            <div class="w-2 rounded-t bg-emerald-500/70 dark:bg-emerald-500/50" style="height: {{ $maxProjects ? ($p / $maxProjects) * 60 : 0 }}px; min-height: {{ $p ? 4 : 0 }}px;"></div>
                                            <div class="w-2 rounded-t bg-sky-500/70 dark:bg-sky-500/50" style="height: {{ $maxListings ? ($l / $maxListings) * 60 : 0 }}px; min-height: {{ $l ? 4 : 0 }}px;"></div>
                                            <div class="w-2 rounded-t bg-amber-500/70 dark:bg-amber-500/50" style="height: {{ $maxDealers ? ($dr / $maxDealers) * 60 : 0 }}px; min-height: {{ $dr ? 4 : 0 }}px;"></div>
                                        </div>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 transform -rotate-45 origin-left">{{ \Carbon\Carbon::parse($d)->format('M j') }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex gap-4 mt-3 text-xs text-slate-500 dark:text-slate-400">
                                <span><span class="inline-block w-2 h-2 rounded bg-emerald-500/70"></span> Projects</span>
                                <span><span class="inline-block w-2 h-2 rounded bg-sky-500/70"></span> Listings</span>
                                <span><span class="inline-block w-2 h-2 rounded bg-amber-500/70"></span> Dealers</span>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
