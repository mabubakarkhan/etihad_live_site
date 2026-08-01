<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ $tag->exists ? 'Edit' : 'Add' }} Tag | Etihad Admin</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')
            <main class="flex-1">
                <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between">
                    <h1 class="text-xl font-semibold">{{ $tag->exists ? 'Edit tag' : 'Add tag' }}</h1>
                    <a href="{{ route('admin.blog-tags.index') }}" class="text-xs px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700">Back</a>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8">
                    <form method="POST" action="{{ $tag->exists ? route('admin.blog-tags.update', $tag) : route('admin.blog-tags.store') }}" class="max-w-2xl space-y-4">
                        @csrf
                        @if ($tag->exists) @method('PUT') @endif
                        @if ($errors->any())
                            <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800">{{ $errors->first() }}</div>
                        @endif
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-6 space-y-4 shadow-lg">
                            <h2 class="text-sm font-semibold">Details</h2>
                            <div class="space-y-1.5">
                                <label class="block text-sm">Name *</label>
                                <input name="name" type="text" value="{{ old('name', $tag->name) }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm">Slug</label>
                                <input name="slug" type="text" value="{{ old('slug', $tag->slug) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="Auto from name" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm">Description</label>
                                <textarea name="description" rows="3" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('description', $tag->description) }}</textarea>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-6 space-y-4 shadow-lg">
                            <h2 class="text-sm font-semibold">SEO</h2>
                            @include('admin.blog_partials.seo-fields', ['record' => $tag])
                        </div>
                        <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-950 hover:bg-emerald-400">{{ $tag->exists ? 'Save changes' : 'Create tag' }}</button>
                    </form>
                </section>
            </main>
        </div>
    </body>
</html>
