<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit {{ $phase->title }} | DHA | Etihad Admin</title>
    @include('admin.partials.theme-init')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen">
<div class="min-h-screen flex">
    @include('admin.partials.sidebar')
    <main class="flex-1">
        <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800 flex justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-xl font-semibold">{{ $phase->title }}</h1>
                <p class="text-sm text-slate-500 mt-1">
                    <a href="{{ route('admin.dha-phases.index') }}" class="text-sky-600 hover:underline">All phases</a>
                    · <a href="{{ route('dha.phase.show', $phase->slug) }}" target="_blank" class="text-emerald-600 hover:underline">View on site</a>
                    · <a href="{{ route('admin.dealer-listings.index', ['dha_phase' => $phase->id]) }}" class="text-violet-600 hover:underline">Listings in this phase</a>
                </p>
            </div>
        </header>
        <section class="p-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs">{{ session('status') }}</div>
            @endif
            <form id="dha-phase-form" method="POST" action="{{ route('admin.dha-phases.update', $phase) }}" data-upload-url="{{ route('admin.dha-phases.upload-media') }}" data-phase-id="{{ $phase->id }}">
                @csrf
                @method('PUT')
                @include('admin.dha_phases._form')
                <div class="mt-6 flex gap-3 flex-wrap">
                    <button type="submit" class="rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950">Save phase</button>
                </div>
            </form>
            <form method="POST" action="{{ route('admin.dha-phases.destroy', $phase) }}" class="mt-8" onsubmit="return confirm('Delete this phase?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs text-rose-600 hover:underline">Delete phase</button>
            </form>
        </section>
    </main>
</div>
@stack('scripts')
</body>
</html>
