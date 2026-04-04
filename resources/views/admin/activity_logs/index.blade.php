<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Activity Logs | Etihad Admin</title>
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
                            Activity logs
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Audit trail of important admin actions.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.profile.show') }}"
                           class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">
                            My profile
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-flex">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </header>

                <section class="px-6 md:px-8 py-6 md:py-8 space-y-4">
                    {{-- Filters --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 overflow-hidden transition-colors">
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90">
                            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Filters</h2>
                        </div>
                        <form method="GET" action="{{ route('admin.activity_logs.index') }}" class="p-4 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                <div class="space-y-1">
                                    <label for="from_date" class="block text-xs font-medium text-slate-500 dark:text-slate-400">From date</label>
                                    <input
                                        type="date"
                                        id="from_date"
                                        name="from_date"
                                        value="{{ request('from_date') }}"
                                        class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                                    />
                                </div>
                                <div class="space-y-1">
                                    <label for="to_date" class="block text-xs font-medium text-slate-500 dark:text-slate-400">To date</label>
                                    <input
                                        type="date"
                                        id="to_date"
                                        name="to_date"
                                        value="{{ request('to_date') }}"
                                        class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                                    />
                                </div>
                                <div class="space-y-1">
                                    <label for="user_id" class="block text-xs font-medium text-slate-500 dark:text-slate-400">User</label>
                                    <select
                                        id="user_id"
                                        name="user_id"
                                        class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                                    >
                                        <option value="">All users</option>
                                        @foreach ($users as $u)
                                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                                {{ $u->username }} ({{ $u->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label for="action" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Action</label>
                                    <select
                                        id="action"
                                        name="action"
                                        class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                                    >
                                        <option value="">All actions</option>
                                        @foreach ($actions as $a)
                                            <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>
                                                {{ $a }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label for="search" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Search</label>
                                    <input
                                        type="text"
                                        id="search"
                                        name="search"
                                        value="{{ request('search') }}"
                                        placeholder="Description or action..."
                                        class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                                    />
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition"
                                >
                                    Apply filters
                                </button>
                                <a
                                    href="{{ route('admin.activity_logs.index') }}"
                                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                >
                                    Clear
                                </a>
                                @if (request()->hasAny(['from_date', 'to_date', 'user_id', 'action', 'search']))
                                    <span class="text-xs text-slate-500 dark:text-slate-500 ml-2">Filters active</span>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 overflow-hidden transition-colors admin-datatable-wrapper">
                        <table class="min-w-full text-sm admin-datatable">
                            <thead class="bg-slate-100 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-xs uppercase text-slate-500 dark:text-slate-400">
                                <tr>
                                    <th class="px-4 py-2 text-left">Time</th>
                                    <th class="px-4 py-2 text-left">User</th>
                                    <th class="px-4 py-2 text-left">Action</th>
                                    <th class="px-4 py-2 text-left">Description</th>
                                    <th class="px-4 py-2 text-left">IP</th>
                                    <th class="px-4 py-2 text-left">User Agent</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse ($logs as $log)
                                    <tr class="bg-white dark:bg-slate-900/50">
                                        <td class="px-4 py-2 text-slate-700 dark:text-slate-300">
                                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                                        </td>
                                        <td class="px-4 py-2 text-slate-800 dark:text-slate-200">
                                            @if ($log->user)
                                                {{ $log->user->username }} ({{ $log->user->email }})
                                            @else
                                                <span class="text-[11px] text-slate-500 dark:text-slate-500">system</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-[11px]">
                                            <span class="inline-flex items-center rounded-full bg-slate-200 dark:bg-slate-800 px-2 py-0.5 text-[11px] text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-slate-800 dark:text-slate-200">
                                            {{ $log->description ?? '—' }}
                                        </td>
                                        <td class="px-4 py-2 text-slate-500 dark:text-slate-400 text-xs">
                                            {{ $log->ip_address ?? '—' }}
                                        </td>
                                        <td class="px-4 py-2 text-slate-500 dark:text-slate-500 text-xs max-w-xs truncate" title="{{ $log->user_agent }}">
                                            {{ $log->user_agent ?? '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-empty>
                                        <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-500">
                                            @if (request()->hasAny(['from_date', 'to_date', 'user_id', 'action', 'search']))
                                                No activity matching your filters.
                                            @else
                                                No activity recorded yet.
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
