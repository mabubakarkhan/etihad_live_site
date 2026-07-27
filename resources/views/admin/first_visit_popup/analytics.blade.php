<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Site analytics | Etihad Admin</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')
            <main class="flex-1 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 transition-colors">
                <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Site analytics</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Page visits and first-time visitors across the whole website.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.first-visit-popup.edit') }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">Edit popup</a>
                        @include('admin.partials.theme-toggle')
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-4 shadow-lg">
                            <p class="text-xs uppercase tracking-wider text-slate-500">Page views today</p>
                            <p class="mt-2 text-2xl font-semibold">{{ number_format($totals['page_views_today']) }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-4 shadow-lg">
                            <p class="text-xs uppercase tracking-wider text-slate-500">First visitors today</p>
                            <p class="mt-2 text-2xl font-semibold">{{ number_format($totals['first_visitors_today']) }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-4 shadow-lg">
                            <p class="text-xs uppercase tracking-wider text-slate-500">Page views (14 days)</p>
                            <p class="mt-2 text-2xl font-semibold">{{ number_format($totals['page_views_14']) }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-4 shadow-lg">
                            <p class="text-xs uppercase tracking-wider text-slate-500">Popup leads (new / total)</p>
                            <p class="mt-2 text-2xl font-semibold">{{ number_format($totals['popup_leads_new']) }} / {{ number_format($totals['popup_leads']) }}</p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg">
                        <h2 class="text-sm font-semibold mb-4">Last 14 days</h2>
                        <canvas id="site-analytics-chart" height="110"></canvas>
                    </div>

                    <p class="text-xs text-slate-500">
                        Popup is currently <strong>{{ $popup->is_enabled ? 'enabled' : 'disabled' }}</strong>.
                        First-visitor popup leads appear in
                        <a href="{{ route('admin.contact-messages.index', ['source' => 'popup_first_visitor']) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">Contact messages</a>
                        with source “Popup first-time visitor”.
                    </p>
                </section>
            </main>
        </div>
        <script>
            (function () {
                var ctx = document.getElementById('site-analytics-chart');
                if (!ctx || typeof Chart === 'undefined') return;
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($labels),
                        datasets: [
                            {
                                label: 'Page views',
                                data: @json($pageViews),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16,185,129,0.12)',
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'First visitors',
                                data: @json($firstVisitors),
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245,158,11,0.12)',
                                tension: 0.3,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom' } },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                    }
                });
            })();
        </script>
    </body>
</html>
