@php
    $indexRoute = $source === 'project' ? 'admin.requests.projects' : 'admin.requests.properties';
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Request #{{ $request->id }} | Etihad Admin</title>
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
                            Request #{{ $request->id }}
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            <a href="{{ route($indexRoute) }}" class="text-sky-600 dark:text-sky-400 hover:underline">{{ $source === 'project' ? 'Project requests' : 'Property requests' }}</a>
                            · {{ $request->created_at->format('M j, Y H:i') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route($indexRoute) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Back to list</a>
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-flex">@csrf<button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Logout</button></form>
                    </div>
                </header>

                <section class="px-6 md:px-8 py-6 md:py-8 space-y-6 max-w-3xl">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Status</h2>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $request->status === 'new' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                                {{ $request->status }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Opened requests are marked as seen.</p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-3">Source</h2>
                        @if($source === 'project' && $request->project)
                            <p class="text-sm text-slate-700 dark:text-slate-300">Project: <a href="{{ route('admin.projects.preview', $request->project) }}" class="text-sky-600 dark:text-sky-400 hover:underline" target="_blank">{{ $request->project->title }}</a></p>
                        @elseif($source === 'property' && $request->property)
                            <p class="text-sm text-slate-700 dark:text-slate-300">
                                Listing: <a href="{{ $request->dealer_id > 0 ? route('admin.dealer-listings.preview', $request->property) : route('admin.own-listings.preview', $request->property) }}" class="text-sky-600 dark:text-sky-400 hover:underline" target="_blank">{{ $request->property->title }}</a>
                                @if($request->dealer)
                                    <span class="text-slate-500"> (Dealer: {{ $request->dealer->name }})</span>
                                @else
                                    <span class="text-slate-500"> (Own)</span>
                                @endif
                            </p>
                        @else
                            <p class="text-sm text-slate-500">—</p>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-3">Contact</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div>
                                <dt class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</dt>
                                <dd class="text-slate-800 dark:text-slate-200 mt-0.5">{{ $request->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Email</dt>
                                <dd class="text-slate-800 dark:text-slate-200 mt-0.5">{{ $request->email ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Phone</dt>
                                <dd class="text-slate-800 dark:text-slate-200 mt-0.5">{{ $request->phone ?? '—' }}</dd>
                            </div>
                            @if($source === 'project')
                            <div>
                                <dt class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Property type</dt>
                                <dd class="text-slate-800 dark:text-slate-200 mt-0.5">{{ $request->property_type ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Budget</dt>
                                <dd class="text-slate-800 dark:text-slate-200 mt-0.5">{{ $request->budget ?? '—' }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    @if($request->message)
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-3">Message</h2>
                        <div class="prose prose-sm dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 whitespace-pre-wrap">{{ $request->message }}</div>
                    </div>
                    @endif

                    <div class="pt-2">
                        <a href="{{ route($indexRoute) }}" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-emerald-400 transition">Back to list</a>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
