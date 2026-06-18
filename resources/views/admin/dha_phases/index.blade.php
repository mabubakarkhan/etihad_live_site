<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DHA Phases | Etihad Admin</title>
    @include('admin.partials.theme-init')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen">
<div class="min-h-screen flex">
    @include('admin.partials.sidebar')
    <main class="flex-1">
        <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center flex-wrap gap-3">
            <div>
                <h1 class="text-xl font-semibold">DHA Phases</h1>
                <p class="text-sm text-slate-500 mt-1">
                    <a href="{{ route('admin.dha.edit') }}" class="text-sky-600 hover:underline">DHA main page</a>
                    · <a href="{{ route('dha.index') }}" target="_blank" class="text-emerald-600 hover:underline">View DHA on site</a>
                </p>
            </div>
            <a href="{{ route('admin.dha-phases.create') }}" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950">Add phase</a>
        </header>
        <section class="p-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs">{{ session('status') }}</div>
            @endif
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-950/60 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Phase</th>
                            <th class="px-4 py-3">Listings</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($phases as $phase)
                        <tr>
                            <td class="px-4 py-3">{{ $phase->sort_order }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $phase->title }}</div>
                                <div class="text-xs text-slate-500">{{ $phase->slug }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.dealer-listings.index', ['dha_phase' => $phase->id]) }}" class="text-sky-600 hover:underline">{{ $phase->active_listings_count }} active</a>
                            </td>
                            <td class="px-4 py-3"><span class="text-xs px-2 py-0.5 rounded {{ $phase->status === 'active' ? 'bg-emerald-500/20 text-emerald-700' : 'bg-slate-500/20' }}">{{ $phase->status }}</span></td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('dha.phase.show', $phase->slug) }}" target="_blank" class="text-emerald-600 hover:underline text-xs">View</a>
                                <a href="{{ route('admin.dha-phases.edit', $phase) }}" class="text-sky-600 hover:underline text-xs">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>
