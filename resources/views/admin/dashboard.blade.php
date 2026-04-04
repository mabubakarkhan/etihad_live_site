<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Admin Panel | Etihad</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')

            <!-- Main content -->
            <main class="flex-1 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 transition-colors">
                <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">
                            Admin Dashboard
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            High-level overview of your system at a glance.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.profile.show') }}"
                           class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">
                            My profile
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-flex">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-500 text-slate-950 hover:bg-emerald-400 transition shadow shadow-emerald-500/40">
                                <span class="h-2 w-2 rounded-full bg-emerald-900/60 border border-emerald-700"></span>
                                Logout
                            </button>
                        </form>
                    </div>
                </header>

                <section class="px-6 md:px-8 py-6 md:py-8 space-y-6">
                    <!-- KPI cards -->
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="relative overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/70 p-4 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs font-medium tracking-[0.18em] uppercase text-slate-500 dark:text-slate-500">
                                    Dealers This Week
                                </p>
                                @if($dealersPercentChange !== 0)
                                    <span class="text-[11px] {{ $dealersPercentChange >= 0 ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border-emerald-500/30' : 'text-rose-600 dark:text-rose-400 bg-rose-500/10 border-rose-500/30' }} border rounded-full px-2 py-0.5">
                                        {{ $dealersPercentChange >= 0 ? '+' : '' }}{{ $dealersPercentChange }}% vs last week
                                    </span>
                                @endif
                            </div>
                            <p class="text-3xl font-semibold text-slate-900 dark:text-slate-50">
                                {{ number_format($dealersThisWeek) }}
                            </p>
                            <p class="text-xs text-slate-500 mt-2">
                                New dealers registered in the last 7 days.
                            </p>
                            <div class="absolute -right-8 -bottom-10 h-28 w-28 rounded-full bg-emerald-500/5 border border-emerald-500/20"></div>
                        </div>

                        <div class="relative overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/70 p-4 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs font-medium tracking-[0.18em] uppercase text-slate-500 dark:text-slate-500">
                                    Active Projects
                                </p>
                                <span class="h-2 w-2 rounded-full bg-emerald-500 dark:bg-emerald-400 shadow-[0_0_0_4px_rgba(74,222,128,0.15)]"></span>
                            </div>
                            <p class="text-3xl font-semibold text-slate-900 dark:text-slate-50">
                                {{ number_format($activeProjects) }}
                            </p>
                            <p class="text-xs text-slate-500 mt-2">
                                Projects with status &ldquo;Active&rdquo; — shown on dashboard and frontend.
                            </p>
                            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-2xl bg-sky-500/5 border border-sky-500/20 rotate-6"></div>
                        </div>

                        <div class="relative overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/70 p-4 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs font-medium tracking-[0.18em] uppercase text-slate-500 dark:text-slate-500">
                                    Active Listings
                                </p>
                                <span class="h-2 w-2 rounded-full bg-emerald-500 dark:bg-emerald-400 shadow-[0_0_0_4px_rgba(74,222,128,0.15)]"></span>
                            </div>
                            <p class="text-3xl font-semibold text-slate-900 dark:text-slate-50">
                                {{ number_format($activeListings) }}
                            </p>
                            <p class="text-xs text-slate-500 mt-2">
                                Listings with status &ldquo;Active&rdquo; — ready for display.
                            </p>
                            <div class="absolute -left-10 -bottom-8 h-28 w-28 rounded-full bg-emerald-500/5 border border-emerald-500/10 blur-2xl"></div>
                        </div>
                    </div>

                    <!-- Wide stats graph (projects, own listing, dealer listing, dealers) -->
                    <div class="rounded-2xl border border-emerald-500/40 bg-gradient-to-b from-emerald-500/15 via-white to-slate-100 dark:via-slate-900 dark:to-slate-950 p-4 md:p-5 shadow-lg shadow-emerald-900/20 dark:shadow-emerald-900/50 transition-colors">
                        <p class="text-xs font-semibold tracking-[0.18em] text-emerald-700 dark:text-emerald-300 uppercase mb-2">Overview — Last 30 days</p>
                        <div class="h-[220px] w-full overflow-hidden">
                            <canvas id="dashboardWideChart" class="w-full h-full" style="display: block; height: 220px; width: 100%;"></canvas>
                        </div>
                    </div>

                    <!-- All counters and states -->
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/70 p-4 md:p-5 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-xs font-semibold tracking-[0.18em] text-slate-500 dark:text-slate-500 uppercase">
                                All Counters &amp; States
                            </p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 p-3">
                                <p class="text-[11px] font-medium uppercase text-slate-500 dark:text-slate-400 tracking-wider">Total Dealers</p>
                                <p class="text-2xl font-semibold text-slate-900 dark:text-slate-50 mt-0.5">{{ number_format($totalDealers) }}</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 p-3">
                                <p class="text-[11px] font-medium uppercase text-slate-500 dark:text-slate-400 tracking-wider">Total Projects</p>
                                <p class="text-2xl font-semibold text-slate-900 dark:text-slate-50 mt-0.5">{{ number_format($totalProjects) }}</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 p-3">
                                <p class="text-[11px] font-medium uppercase text-slate-500 dark:text-slate-400 tracking-wider">Total Listings</p>
                                <p class="text-2xl font-semibold text-slate-900 dark:text-slate-50 mt-0.5">{{ number_format($totalListings) }}</p>
                            </div>
                        </div>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/50 p-4">
                                <p class="text-[11px] font-semibold uppercase text-slate-500 dark:text-slate-400 tracking-wider border-b border-slate-200 dark:border-slate-600 pb-2 mb-2">Projects by status</p>
                                <ul class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-600 dark:text-slate-300">
                                    <li>Active: {{ $projectsByStatus['active'] }}</li>
                                    <li>Hold: {{ $projectsByStatus['hold'] }}</li>
                                    <li>Inactive: {{ $projectsByStatus['inactive'] }}</li>
                                    <li>Close: {{ $projectsByStatus['close'] }}</li>
                                </ul>
                            </div>
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/50 p-4">
                                <p class="text-[11px] font-semibold uppercase text-slate-500 dark:text-slate-400 tracking-wider border-b border-slate-200 dark:border-slate-600 pb-2 mb-2">Listings by status</p>
                                <ul class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-600 dark:text-slate-300">
                                    <li>Active: {{ $listingsByStatus['active'] }}</li>
                                    <li>Hold: {{ $listingsByStatus['hold'] }}</li>
                                    <li>Inactive: {{ $listingsByStatus['inactive'] }}</li>
                                    <li>Close: {{ $listingsByStatus['close'] }}</li>
                                </ul>
                            </div>
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/50 p-4">
                                <p class="text-[11px] font-semibold uppercase text-slate-500 dark:text-slate-400 tracking-wider border-b border-slate-200 dark:border-slate-600 pb-2 mb-2">Listings by purpose</p>
                                <ul class="text-xs mt-1 space-y-1.5">
                                    <li class="text-slate-600 dark:text-slate-300">Own — <a href="{{ route('admin.own-listings.index', ['purpose' => 'sale']) }}" class="text-sky-600 dark:text-sky-400 hover:underline">Sale: {{ $listingsByPurpose['own_sale'] ?? 0 }}</a>, <a href="{{ route('admin.own-listings.index', ['purpose' => 'rent']) }}" class="text-sky-600 dark:text-sky-400 hover:underline">Rent: {{ $listingsByPurpose['own_rent'] ?? 0 }}</a></li>
                                    <li class="text-slate-600 dark:text-slate-300">Dealer — <a href="{{ route('admin.dealer-listings.index', ['purpose' => 'sale']) }}" class="text-sky-600 dark:text-sky-400 hover:underline">Sale: {{ $listingsByPurpose['dealer_sale'] ?? 0 }}</a>, <a href="{{ route('admin.dealer-listings.index', ['purpose' => 'rent']) }}" class="text-sky-600 dark:text-sky-400 hover:underline">Rent: {{ $listingsByPurpose['dealer_rent'] ?? 0 }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Activity & notes -->
                    <div class="grid gap-4 lg:grid-cols-3">
                        <div class="lg:col-span-2 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-4 md:p-5 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs font-semibold tracking-[0.18em] text-slate-500 uppercase">
                                    Recent Activity
                                </p>
                                <a href="{{ route('admin.activity_logs.index') }}" class="text-[11px] text-sky-600 dark:text-sky-400 hover:underline">View all</a>
                            </div>

                            <div class="space-y-3 text-sm">
                                @forelse($recentActivity ?? [] as $index => $log)
                                    <div class="flex items-start gap-3">
                                        @if($index === 0)
                                            <div class="mt-1 h-6 w-6 rounded-full bg-emerald-500/10 border border-emerald-500/40 flex items-center justify-center text-[11px] text-emerald-600 dark:text-emerald-300 flex-shrink-0">1</div>
                                        @elseif($index === 1)
                                            <div class="mt-1 h-6 w-6 rounded-full bg-sky-500/10 border border-sky-500/40 flex items-center justify-center text-[11px] text-sky-600 dark:text-sky-300 flex-shrink-0">2</div>
                                        @else
                                            <div class="mt-1 h-6 w-6 rounded-full bg-purple-500/10 border border-purple-500/40 flex items-center justify-center text-[11px] text-purple-600 dark:text-purple-300 flex-shrink-0">3</div>
                                        @endif
                                        <div class="flex-1 min-w-0 {{ !$loop->last ? 'border-b border-slate-200 dark:border-slate-800/80 pb-3' : '' }}">
                                            <p class="text-slate-800 dark:text-slate-100">
                                                {{ str_replace('_', ' ', ucfirst($log->action ?? 'Activity')) }}
                                            </p>
                                            <p class="text-xs text-slate-500 mt-1">
                                                {{ $log->description ?: '—' }}
                                            </p>
                                            @if($log->user)
                                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $log->user->username ?? $log->user->name }} · {{ $log->created_at->diffForHumans() }}</p>
                                            @else
                                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-500 dark:text-slate-400 py-2">No recent activity.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-2xl border border-emerald-500/40 bg-gradient-to-b from-emerald-500/15 via-white to-slate-100 dark:via-slate-900 dark:to-slate-950 p-3 md:p-4 shadow-lg shadow-emerald-900/20 dark:shadow-emerald-900/50 transition-colors flex flex-col">
                            <p class="text-xs font-semibold tracking-[0.18em] text-emerald-700 dark:text-emerald-300 uppercase mb-2">Shortcuts &amp; insights</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 text-xs mb-3">
                                <ul class="space-y-1.5 text-slate-700 dark:text-slate-200">
                                    <li class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400">Projects (30d)</span><a href="{{ route('admin.projects.index') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline font-medium">{{ $projectsLast30 ?? 0 }}</a></li>
                                    <li class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400">Listings (30d)</span><a href="{{ route('admin.own-listings.index') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline font-medium">{{ $listingsLast30 ?? 0 }}</a></li>
                                    <li class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400">Dealers (100d)</span><a href="{{ route('admin.dealers.index') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline font-medium">{{ $dealersLast100 ?? 0 }}</a></li>
                                    <li class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400">Projects on hold</span><a href="{{ route('admin.projects.index', ['status' => 'hold']) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline font-medium">{{ $projectsByStatus['hold'] ?? 0 }}</a></li>
                                </ul>
                                <ul class="space-y-1.5 text-slate-700 dark:text-slate-200">
                                    <li class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400">Inactive listings</span><a href="{{ route('admin.own-listings.index', ['status' => 'inactive']) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline font-medium">{{ $listingsByStatus['inactive'] ?? 0 }}</a></li>
                                    <li class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400">Closed projects</span><a href="{{ route('admin.projects.index', ['status' => 'close']) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline font-medium">{{ $projectsByStatus['close'] ?? 0 }}</a></li>
                                    <li class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400">Reports</span><a href="{{ route('admin.reports.index') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">View</a></li>
                                    <li class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400">Activity logs</span><a href="{{ route('admin.activity_logs.index') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">View</a></li>
                                </ul>
                            </div>
                            <div class="rounded-lg border border-emerald-500/20 bg-transparent dark:bg-slate-900/30 p-2 h-[160px] overflow-hidden">
                                <p class="text-[10px] font-semibold text-emerald-700/80 dark:text-emerald-400/80 uppercase tracking-wider mb-1">Visitors — Past 7 days</p>
                                <div class="h-[120px] w-full">
                                    <canvas id="dashboardChart" class="w-full h-full" style="display: block; height: 120px; width: 100%;"></canvas>
                                </div>
                            </div>
                            <div class="mt-3 pt-2 border-t border-emerald-500/20 flex flex-wrap gap-1.5">
                                <a href="{{ route('admin.projects.create') }}" class="text-[11px] px-2 py-1 rounded bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/20 transition">+ Project</a>
                                <a href="{{ route('admin.own-listings.create') }}" class="text-[11px] px-2 py-1 rounded bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/20 transition">+ Listing</a>
                                <a href="{{ route('admin.dealers.index') }}" class="text-[11px] px-2 py-1 rounded bg-slate-200/80 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 hover:opacity-90 transition">Dealers</a>
                                <a href="{{ route('admin.project_types.index') }}" class="text-[11px] px-2 py-1 rounded bg-slate-200/80 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 hover:opacity-90 transition">Types</a>
                                <a href="{{ route('admin.reports.index') }}" class="text-[11px] px-2 py-1 rounded bg-slate-200/80 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 hover:opacity-90 transition">Reports</a>
                                <a href="{{ route('admin.activity_logs.index') }}" class="text-[11px] px-2 py-1 rounded bg-slate-200/80 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 hover:opacity-90 transition">Logs</a>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var isDark = document.documentElement.classList.contains('dark');
            var gridColor = isDark ? 'rgba(51, 65, 85, 0.35)' : 'rgba(16, 185, 129, 0.06)';
            var textColor = isDark ? '#94a3b8' : '#475569';
            var chartLabels = @json($chartLabels ?? []);
            var chartLabels7 = @json($chartLabels7 ?? []);
            var nanoSmall = {
                visitors: '#00CCFF',
                ownListing: '#00CCFF',
                dealerListing: '#AA80FF',
                projects: '#00E0A0'
            };

            var ctxSmall = document.getElementById('dashboardChart');
            if (ctxSmall) {
                new Chart(ctxSmall, {
                    type: 'line',
                    data: {
                        labels: chartLabels7,
                        datasets: [
                            { label: 'Total', data: @json($chartVisitor7 ?? []), borderColor: nanoSmall.visitors, backgroundColor: 'transparent', borderWidth: 2, fill: false, tension: 0.3, pointRadius: 0, pointHoverRadius: 4, pointHoverBackgroundColor: nanoSmall.visitors },
                            { label: 'Own', data: @json($chartVisitorOwnListing7 ?? []), borderColor: nanoSmall.ownListing, backgroundColor: 'transparent', borderWidth: 2, fill: false, tension: 0.3, pointRadius: 0, pointHoverRadius: 4, pointHoverBackgroundColor: nanoSmall.ownListing },
                            { label: 'Dealers', data: @json($chartVisitorDealerListing7 ?? []), borderColor: nanoSmall.dealerListing, backgroundColor: 'transparent', borderWidth: 2, fill: false, tension: 0.3, pointRadius: 0, pointHoverRadius: 4, pointHoverBackgroundColor: nanoSmall.dealerListing },
                            { label: 'Projects', data: @json($chartVisitorProjects7 ?? []), borderColor: nanoSmall.projects, backgroundColor: 'transparent', borderWidth: 2, fill: false, tension: 0.3, pointRadius: 0, pointHoverRadius: 4, pointHoverBackgroundColor: nanoSmall.projects }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: { legend: { display: true, position: 'bottom', align: 'center', labels: { boxWidth: 12, usePointStyle: true, font: { size: 10 }, color: textColor, padding: 12 } } },
                        scales: {
                            x: { grid: { display: true, color: gridColor }, border: { display: false }, ticks: { maxTicksLimit: 8, font: { size: 9 }, color: textColor } },
                            y: { beginAtZero: true, grid: { display: true, color: gridColor }, border: { display: false }, ticks: { stepSize: 1, font: { size: 9 }, color: textColor } }
                        }
                    }
                });
            }

            var ctxWide = document.getElementById('dashboardWideChart');
            if (ctxWide) {
                var nanoWide = {
                    project: isDark ? 'rgba(110, 231, 183, 0.95)' : 'rgba(52, 211, 153, 0.9)',
                    ownListing: isDark ? 'rgba(125, 211, 252, 0.95)' : 'rgba(56, 189, 248, 0.9)',
                    dealerListing: isDark ? 'rgba(196, 181, 253, 0.95)' : 'rgba(129, 140, 248, 0.9)',
                    dealers: isDark ? 'rgba(103, 232, 249, 0.95)' : 'rgba(34, 211, 238, 0.9)'
                };
                new Chart(ctxWide, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [
                            { label: 'Projects', data: @json($chartDailyProjects ?? []), borderColor: nanoWide.project, backgroundColor: 'transparent', borderWidth: 2, fill: false, tension: 0.3, pointRadius: 0, pointHoverRadius: 4, pointHoverBackgroundColor: nanoWide.project },
                            { label: 'Own listing', data: @json($chartDailyOwnListings ?? []), borderColor: nanoWide.ownListing, backgroundColor: 'transparent', borderWidth: 2, fill: false, tension: 0.3, pointRadius: 0, pointHoverRadius: 4, pointHoverBackgroundColor: nanoWide.ownListing },
                            { label: 'Dealer listing', data: @json($chartDailyDealerListings ?? []), borderColor: nanoWide.dealerListing, backgroundColor: 'transparent', borderWidth: 2, fill: false, tension: 0.3, pointRadius: 0, pointHoverRadius: 4, pointHoverBackgroundColor: nanoWide.dealerListing },
                            { label: 'Dealers', data: @json($chartDailyDealers ?? []), borderColor: nanoWide.dealers, backgroundColor: 'transparent', borderWidth: 2, fill: false, tension: 0.3, pointRadius: 0, pointHoverRadius: 4, pointHoverBackgroundColor: nanoWide.dealers }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: { legend: { display: true, position: 'bottom', align: 'center', labels: { boxWidth: 12, usePointStyle: true, font: { size: 10 }, color: textColor, padding: 10 } } },
                        scales: {
                            x: { grid: { display: true, color: gridColor }, border: { display: false }, ticks: { maxTicksLimit: 12, font: { size: 9 }, color: textColor } },
                            y: { beginAtZero: true, grid: { display: true, color: gridColor }, border: { display: false }, ticks: { stepSize: 1, font: { size: 9 }, color: textColor } }
                        }
                    }
                });
            }
        });
        </script>
    </body>
</html>

