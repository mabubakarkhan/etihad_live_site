<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Interactive Map · {{ $ownerLabel }} | Etihad Admin</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')
            <main class="flex-1 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 overflow-auto transition-colors">
                <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between flex-wrap gap-3 sticky top-0 bg-slate-100/95 dark:bg-slate-950/95 z-20">
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Interactive Map</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $ownerLabel }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Back to editor</a>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8">
                    @include('admin.interactive-map._editor', [
                        'ownerType' => $ownerType,
                        'ownerId' => $ownerId,
                        'map' => $map,
                        'standaloneUrl' => null,
                    ])
                </section>
            </main>
        </div>
    </body>
</html>
