<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Blog Posts | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Blog posts</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage news articles, SEO, media, categories and tags.</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.blog-posts.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-500 text-slate-950 hover:bg-emerald-400 transition shadow shadow-emerald-500/40">Add post</a>
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-flex">@csrf<button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Logout</button></form>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8 space-y-4">
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden">
                        <form method="GET" action="{{ route('admin.blog-posts.index') }}" class="p-4 flex flex-wrap items-end gap-4">
                            <div class="space-y-1">
                                <label for="filter-status" class="block text-xs font-medium text-slate-500">Status</label>
                                <select id="filter-status" name="status" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm">
                                    <option value="">All</option>
                                    <option value="published" {{ ($filterStatus ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="draft" {{ ($filterStatus ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                                </select>
                            </div>
                            <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-emerald-400">Apply</button>
                            @if (!empty($filterStatus))
                                <a href="{{ route('admin.blog-posts.index') }}" class="rounded-lg border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm">Clear</a>
                            @endif
                        </form>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden admin-datatable-wrapper">
                        <table class="min-w-full text-sm admin-datatable">
                            <thead class="bg-slate-100 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-xs uppercase text-slate-500">
                                <tr>
                                    <th class="px-4 py-2 text-left">Title</th>
                                    <th class="px-4 py-2 text-left">Categories</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Published</th>
                                    <th class="px-4 py-2 text-left">Author</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse ($posts as $post)
                                    <tr>
                                        <td class="px-4 py-2 font-medium text-slate-900 dark:text-slate-100">
                                            <a href="{{ $post->url() }}" target="_blank" class="hover:text-emerald-600">{{ \Illuminate\Support\Str::limit($post->title, 70) }}</a>
                                        </td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ $post->categories->pluck('name')->join(', ') ?: '—' }}</td>
                                        <td class="px-4 py-2">
                                            @if ($post->status === 'published')
                                                <span class="inline-flex rounded-full bg-emerald-500/15 px-2 py-0.5 text-[11px] font-medium text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">Published</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-amber-500/15 px-2 py-0.5 text-[11px] font-medium text-amber-700 dark:text-amber-300 border border-amber-500/30">Draft</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ optional($post->published_at)->format('Y-m-d H:i') ?: '—' }}</td>
                                        <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ optional($post->author)->name ?: '—' }}</td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('admin.blog-posts.edit', $post) }}" class="text-[11px] px-2 py-1 rounded border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800">Edit</a>
                                            <form method="POST" action="{{ route('admin.blog-posts.destroy', $post) }}" class="inline-block ml-1" onsubmit="return confirm('Delete this post?');">@csrf @method('DELETE')<button type="submit" class="text-[11px] px-2 py-1 rounded border border-rose-600/60 text-rose-600 hover:bg-rose-600/10">Delete</button></form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-empty><td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No posts yet. <a href="{{ route('admin.blog-posts.create') }}" class="text-emerald-600">Add post</a>.</td></tr>
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
