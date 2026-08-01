<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Blog Tags | Etihad Admin</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')
            <main class="flex-1">
                <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold">Blog tags</h1>
                        <p class="text-sm text-slate-500 mt-1">Manage post tags.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.blog-tags.create') }}" class="inline-flex px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-500 text-slate-950 hover:bg-emerald-400">Add tag</a>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8 space-y-4">
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden admin-datatable-wrapper">
                        <table class="min-w-full text-sm admin-datatable">
                            <thead class="bg-slate-100 dark:bg-slate-900/90 border-b text-xs uppercase text-slate-500">
                                <tr>
                                    <th class="px-4 py-2 text-left">Name</th>
                                    <th class="px-4 py-2 text-left">Slug</th>
                                    <th class="px-4 py-2 text-left">Posts</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse ($tags as $tag)
                                    <tr>
                                        <td class="px-4 py-2 font-medium">{{ $tag->name }}</td>
                                        <td class="px-4 py-2 text-slate-500">{{ $tag->slug }}</td>
                                        <td class="px-4 py-2">{{ $tag->posts_count }}</td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('admin.blog-tags.edit', $tag) }}" class="text-[11px] px-2 py-1 rounded border border-slate-300 dark:border-slate-700">Edit</a>
                                            <form method="POST" action="{{ route('admin.blog-tags.destroy', $tag) }}" class="inline ml-1" onsubmit="return confirm('Delete this tag?');">@csrf @method('DELETE')<button type="submit" class="text-[11px] px-2 py-1 rounded border border-rose-600/60 text-rose-600">Delete</button></form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-empty><td colspan="4" class="px-4 py-6 text-center text-slate-500">No tags yet.</td></tr>
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
