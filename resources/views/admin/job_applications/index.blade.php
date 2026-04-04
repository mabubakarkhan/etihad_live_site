<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Job Applications | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Job Applications</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage CVs and applicant status.</p>
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
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                        <form method="GET" action="{{ route('admin.job-applications.index') }}" class="p-4 flex flex-wrap items-end gap-4">
                            <div class="space-y-1">
                                <label for="career_id" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Job</label>
                                <select id="career_id" name="career_id" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                                    <option value="">All jobs</option>
                                    @foreach($careers as $c)
                                    <option value="{{ $c->id }}" {{ request('career_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label for="status" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Status</label>
                                <select id="status" name="status" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                                    <option value="">All</option>
                                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                                    <option value="seen" {{ request('status') === 'seen' ? 'selected' : '' }}>Seen</option>
                                    <option value="accept" {{ request('status') === 'accept' ? 'selected' : '' }}>Accept</option>
                                    <option value="considering" {{ request('status') === 'considering' ? 'selected' : '' }}>Considering</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-emerald-400 transition">Apply</button>
                            @if(request()->hasAny(['career_id','status']))
                                <a href="{{ route('admin.job-applications.index') }}" class="rounded-lg border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Clear</a>
                            @endif
                        </form>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors admin-datatable-wrapper">
                        <table class="min-w-full text-sm admin-datatable">
                            <thead class="bg-slate-100 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-xs uppercase text-slate-500 dark:text-slate-400">
                                <tr>
                                    <th class="px-4 py-2 text-left">Applicant</th>
                                    <th class="px-4 py-2 text-left">Job</th>
                                    <th class="px-4 py-2 text-left">Mobile</th>
                                    <th class="px-4 py-2 text-left">City</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Applied</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse ($applications as $app)
                                    <tr class="bg-white dark:bg-slate-900/50">
                                        <td class="px-4 py-2 text-slate-900 dark:text-slate-100 font-medium">{{ $app->name }}</td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ $app->career->title ?? '—' }}</td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ $app->mobile }}</td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ $app->city ?? '—' }}</td>
                                        <td class="px-4 py-2">
                                            @if($app->status === 'new')<span class="inline-flex rounded-full bg-amber-500/15 px-2 py-0.5 text-[11px] font-medium text-amber-700 dark:text-amber-300">New</span>
                                            @elseif($app->status === 'seen')<span class="inline-flex rounded-full bg-sky-500/15 px-2 py-0.5 text-[11px] font-medium text-sky-700 dark:text-sky-300">Seen</span>
                                            @elseif($app->status === 'accept')<span class="inline-flex rounded-full bg-emerald-500/15 px-2 py-0.5 text-[11px] font-medium text-emerald-700 dark:text-emerald-300">Accept</span>
                                            @elseif($app->status === 'considering')<span class="inline-flex rounded-full bg-slate-400/20 px-2 py-0.5 text-[11px] font-medium text-slate-600 dark:text-slate-400">Considering</span>
                                            @else<span class="inline-flex rounded-full bg-rose-500/15 px-2 py-0.5 text-[11px] font-medium text-rose-700 dark:text-rose-300">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ $app->created_at->format('d M Y') }}</td>
                                        <td class="px-4 py-2"><a href="{{ route('admin.job-applications.show', $app) }}" class="text-[11px] px-2 py-1 rounded border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">View</a></td>
                                    </tr>
                                @empty
                                    <tr data-empty><td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">No applications yet.</td></tr>
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
