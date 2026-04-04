<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Add portal hero slide | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Add hero slide</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Upload a wide image for the portal homepage hero background.</p>
                    </div>
                    <a href="{{ route('admin.portal-hero.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Back</a>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8">
                    <div class="max-w-2xl rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-6 shadow-lg">
                        @if ($errors->any())
                            <div class="mb-4 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif
                        <form method="POST" action="{{ route('admin.portal-hero.store') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="space-y-1.5">
                                <label for="image" class="block text-sm text-slate-700 dark:text-slate-300">Image *</label>
                                <input id="image" name="image" type="file" accept="image/*" required class="block w-full text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-500/20 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-emerald-800 dark:file:text-emerald-200" />
                                <p class="text-xs text-slate-500 dark:text-slate-400">Max 8 MB. Recommended ~1920×900px or wider to match full-bleed hero.</p>
                            </div>
                            <div class="space-y-1.5">
                                <label for="sort_order" class="block text-sm text-slate-700 dark:text-slate-300">Sort order</label>
                                <input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', 0) }}" min="0" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" />
                                <p class="text-xs text-slate-500 dark:text-slate-400">Lower numbers appear first.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-600" />
                                <label for="is_active" class="text-sm text-slate-700 dark:text-slate-300">Active (shown on portal)</label>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Create</button>
                        </form>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
