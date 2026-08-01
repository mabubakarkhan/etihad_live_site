<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Add Blog Post | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight">Add blog post</h1>
                        <p class="text-sm text-slate-500 mt-1">Create a new news article.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.blog-posts.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700">Back</a>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8">
                    <form method="POST" action="{{ route('admin.blog-posts.store') }}" enctype="multipart/form-data" class="max-w-4xl space-y-4">
                        @csrf
                        @if ($errors->any())
                            <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif
                        @include('admin.blog_posts._form')
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400">Create post</button>
                    </form>
                </section>
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
