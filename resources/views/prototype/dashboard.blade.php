<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prototype Dashboard | Etihad GIS POC</title>
    @include('prototype.partials.head-meta')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="{{ asset('theme/css/prototype/map-overlay.css') }}">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
    <div class="prototype-shell">
        <header class="prototype-header">
            <div>
                <p class="prototype-badge">Internal POC</p>
                <h1 class="text-2xl font-semibold tracking-tight">Interactive Map Prototype</h1>
                <p class="text-sm text-slate-400 mt-1">Isolated GIS overlay research environment — not indexed, not production.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('prototype.interactive-map') }}" class="prototype-btn prototype-btn--primary">Open Live Map</a>
                <a href="{{ route('admin.prototype.interactive-map.index') }}" class="prototype-btn">Admin Editor</a>
            </div>
        </header>

        <main class="prototype-main">
            <section class="prototype-card">
                <h2 class="text-lg font-semibold mb-4">Overlay Library</h2>
                @if($overlays->isEmpty())
                    <p class="text-slate-400 text-sm">No overlays yet. Create one in the <a href="{{ route('admin.prototype.interactive-map.index') }}" class="text-emerald-400 hover:underline">admin editor</a>.</p>
                @else
                    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($overlays as $overlay)
                            <article class="prototype-list-card">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-medium">{{ $overlay->title }}</h3>
                                        <p class="text-xs text-slate-400 mt-1">ID #{{ $overlay->id }} · {{ ucfirst($overlay->status) }}</p>
                                    </div>
                                    <span class="prototype-status prototype-status--{{ $overlay->status }}">{{ $overlay->status }}</span>
                                </div>
                                <div class="mt-4 flex gap-2">
                                    <a href="{{ route('prototype.interactive-map.show', $overlay) }}" class="prototype-btn prototype-btn--sm">Preview</a>
                                    <a href="{{ route('admin.prototype.interactive-map.index', ['overlay' => $overlay->id]) }}" class="prototype-btn prototype-btn--sm">Edit</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </main>
    </div>
</body>
</html>
