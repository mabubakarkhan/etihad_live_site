<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Portal hero slides | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Portal hero slides</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Background slider images for the <strong>/portal</strong> homepage hero. Use wide images (e.g. 1920×900px or larger). For the root homepage single hero image, use <a href="{{ route('admin.homepage-hero.edit') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">Homepage hero</a>.</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.portal-hero.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-500 text-slate-950 hover:bg-emerald-400 transition shadow shadow-emerald-500/40">Add slide</a>
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-flex">@csrf<button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Logout</button></form>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8 space-y-4">
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-100 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-xs uppercase text-slate-500">
                                <tr>
                                    <th class="px-4 py-2 text-left">Preview</th>
                                    <th class="px-4 py-2 text-left">Sort</th>
                                    <th class="px-4 py-2 text-left">Active</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse ($slides as $s)
                                    <tr class="bg-white dark:bg-slate-900/50">
                                        <td class="px-4 py-2">
                                            @if ($s->image)
                                                <img src="{{ url('storage/' . ltrim($s->image, '/')) }}" alt="" class="h-14 w-28 object-cover rounded border border-slate-200 dark:border-slate-700" />
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-slate-700 dark:text-slate-300">{{ $s->sort_order }}</td>
                                        <td class="px-4 py-2">{{ $s->is_active ? 'Yes' : 'No' }}</td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('admin.portal-hero.edit', $s) }}" class="text-[11px] px-2 py-1 rounded border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Edit</a>
                                            <form method="POST" action="{{ route('admin.portal-hero.destroy', $s) }}" class="inline-block ml-1" onsubmit="return confirm('Delete this slide?');">@csrf @method('DELETE')<button type="submit" class="text-[11px] px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-300 hover:bg-rose-600/10">Delete</button></form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No slides — the portal uses default theme backgrounds. <a href="{{ route('admin.portal-hero.create') }}" class="text-emerald-600 dark:text-emerald-400">Add one</a>.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
