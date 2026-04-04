<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Notifications | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Notifications</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">New requests and job applications.</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        @if($notifications->whereNull('read_at')->count() > 0)
                        <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" class="inline-flex">@csrf<button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Mark all as read</button></form>
                        @endif
                        <a href="{{ route('admin.profile.show') }}" class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">My profile</a>
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-flex">@csrf<button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Logout</button></form>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8 space-y-4">
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
                        <ul class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($notifications as $n)
                            <li class="{{ $n->read_at ? 'bg-slate-50/50 dark:bg-slate-900/30' : 'bg-amber-500/5 dark:bg-amber-500/10' }}">
                                @if($n->link)
                                <a href="{{ route('admin.notifications.read', $n) }}" class="block px-6 py-4 hover:bg-slate-100/50 dark:hover:bg-slate-800/30 transition">
                                @else
                                <div class="px-6 py-4">
                                @endif
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $n->title }}</p>
                                            @if($n->body)<p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">{{ \Illuminate\Support\Str::limit($n->body, 120) }}</p>@endif
                                            <p class="text-xs text-slate-500 dark:text-slate-500 mt-1">{{ $n->created_at->format('d M Y H:i') }}</p>
                                        </div>
                                        @if(!$n->read_at)<span class="flex-shrink-0 inline-flex rounded-full bg-amber-500/20 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:text-amber-300">New</span>@endif
                                        @if($n->link)<span class="flex-shrink-0 text-slate-400"><i class="fa-solid fa-chevron-right text-xs"></i></span>@endif
                                    </div>
                                @if($n->link)</a>@else</div>@endif
                            </li>
                            @empty
                            <li class="px-6 py-12 text-center text-sm text-slate-500">No notifications yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
