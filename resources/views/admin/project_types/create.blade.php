<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Create Project Type | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Create Project Type</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Add a new project type. Check where it should appear.</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.project_types.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Back to types</a>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8">
                    <div class="max-w-2xl rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-6 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors">
                        @if ($errors->any())
                            <div class="mb-4 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif
                        <form method="POST" action="{{ route('admin.project_types.store') }}" class="space-y-4">
                            @csrf
                            <div class="space-y-1.5">
                                <label for="name" class="block text-sm text-slate-700 dark:text-slate-300">Name</label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                            </div>
                            <div class="space-y-1.5">
                                <label for="slug" class="block text-sm text-slate-700 dark:text-slate-300">Slug (optional, auto from name)</label>
                                <input id="slug" name="slug" type="text" value="{{ old('slug') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                            </div>
                            <div class="border-t border-slate-200 dark:border-slate-700 pt-4 space-y-3">
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Display this type in:</p>
                                <label class="flex items-center gap-2 text-slate-700 dark:text-slate-300"><input type="checkbox" name="show_in_projects" value="1" {{ old('show_in_projects', true) ? 'checked' : '' }} class="rounded border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-950 text-emerald-500" /> Projects listing</label>
                                <label class="flex items-center gap-2 text-slate-700 dark:text-slate-300"><input type="checkbox" name="show_in_properties" value="1" {{ old('show_in_properties', true) ? 'checked' : '' }} class="rounded border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-950 text-emerald-500" /> Properties listing</label>
                                <label class="flex items-center gap-2 text-slate-700 dark:text-slate-300"><input type="checkbox" name="show_in_dealers" value="1" {{ old('show_in_dealers', true) ? 'checked' : '' }} class="rounded border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-950 text-emerald-500" /> Dealers listing</label>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Save type</button>
                        </form>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
