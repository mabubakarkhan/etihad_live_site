<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Interactive Map Prototype | Etihad Admin</title>
    <meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noimageindex">
    <meta name="googlebot" content="noindex,nofollow,noarchive,nosnippet,noimageindex">
    @include('admin.partials.theme-init')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="{{ asset('theme/css/prototype/map-overlay.css') }}">
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
    <div class="min-h-screen flex">
        @include('admin.partials.sidebar')

        <main class="flex-1 overflow-auto">
            <header class="px-6 md:px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-wrap gap-3 sticky top-0 bg-slate-100/95 dark:bg-slate-950/95 z-20">
                <div>
                    <p class="text-[11px] uppercase tracking-widest text-amber-600 dark:text-amber-400 font-semibold">Development · POC</p>
                    <h1 class="text-xl md:text-2xl font-semibold tracking-tight">Interactive Map Prototype</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">GIS overlay research — isolated from production modules.</p>
                </div>
                <div class="flex items-center gap-2">
                    @include('admin.partials.theme-toggle')
                    @if($selected)
                        <a href="{{ route('prototype.interactive-map.show', $selected) }}" target="_blank" rel="noopener" class="prototype-btn prototype-btn--sm">Preview</a>
                    @endif
                </div>
            </header>

            <section class="p-4 md:p-6 lg:p-8"
                     id="prototype-admin-page"
                     data-csrf="{{ csrf_token() }}"
                     data-store-url="{{ route('admin.prototype.interactive-map.store') }}"
                     data-index-url="{{ route('admin.prototype.interactive-map.index') }}">
                <div id="prototype-alert" class="prototype-alert mb-4" hidden></div>
                <div class="grid gap-4 xl:grid-cols-[280px_minmax(0,1fr)]">
                    <aside class="prototype-card p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="font-semibold text-sm">Overlays</h2>
                            <button type="button" id="prototype-create-overlay" class="prototype-btn prototype-btn--sm prototype-btn--primary">New</button>
                        </div>
                        <div id="prototype-overlay-list" class="space-y-2 max-h-[420px] overflow-y-auto">
                            @forelse($overlays as $overlay)
                                <button type="button"
                                        class="prototype-overlay-item {{ $selected && $selected->id === $overlay->id ? 'is-active' : '' }}"
                                        data-overlay-id="{{ $overlay->id }}">
                                    <span class="font-medium text-sm block truncate">{{ $overlay->title }}</span>
                                    <span class="text-[11px] text-slate-500">{{ ucfirst($overlay->status) }}</span>
                                </button>
                            @empty
                                <p class="text-sm text-slate-500">No overlays yet.</p>
                            @endforelse
                        </div>
                    </aside>

                    @if($selected)
                        @php
                            $prototypeRoutes = [
                                'update' => route('admin.prototype.interactive-map.update', $selected),
                                'upload' => route('admin.prototype.interactive-map.upload', $selected),
                                'deleteImage' => route('admin.prototype.interactive-map.delete-image', $selected),
                                'destroy' => route('admin.prototype.interactive-map.destroy', $selected),
                                'config' => route('admin.prototype.interactive-map.config', $selected),
                                'store' => route('admin.prototype.interactive-map.store'),
                                'index' => route('admin.prototype.interactive-map.index'),
                                'sections' => [
                                    'index' => route('admin.prototype.sections.index', $selected),
                                    'store' => route('admin.prototype.sections.store', $selected),
                                    'update' => route('admin.prototype.sections.update', ['overlay' => $selected, 'section' => '__SECTION__']),
                                    'destroy' => route('admin.prototype.sections.destroy', ['overlay' => $selected, 'section' => '__SECTION__']),
                                ],
                            ];
                        @endphp
                        <div id="prototype-map-editor"
                             class="space-y-4"
                             data-overlay-id="{{ $selected->id }}"
                             data-csrf="{{ csrf_token() }}"
                             data-api-base="{{ url('/admin/prototype/interactive-map/' . $selected->id) }}"
                             data-google-maps-key="{{ $googleMapsApiKey }}"
                             data-google-maps-map-id="{{ $googleMapsMapId }}"
                             data-overlay='@json($selected->toEditorPayload())'
                             data-routes='@json($prototypeRoutes)'>

                            <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_360px]">
                                <div class="prototype-card p-0 overflow-hidden">
                                    <div class="prototype-toolbar" data-prototype-toolbar>
                                        <button type="button" data-tool="fit-bounds" class="prototype-tool-btn" title="Fit bounds">Fit</button>
                                        <button type="button" data-tool="roadmap" class="prototype-tool-btn is-active" title="Roadmap">Road</button>
                                        <button type="button" data-tool="satellite" class="prototype-tool-btn" title="Satellite">Sat</button>
                                        <button type="button" data-tool="hybrid" class="prototype-tool-btn" title="Hybrid">Hybrid</button>
                                        <button type="button" data-tool="terrain" class="prototype-tool-btn" title="Terrain">Terrain</button>
                                        <button type="button" data-tool="toggle-bounds" class="prototype-tool-btn is-active" title="Toggle bounds">Bounds</button>
                                    </div>
                                    <div id="prototype-draw-hint" class="prototype-draw-hint" hidden></div>
                                    <div id="prototype-map-canvas" class="prototype-map-canvas"></div>
                                </div>

                                <div class="space-y-4">
                                    <div class="prototype-card p-4 space-y-4">
                                        <h3 class="font-semibold">Overlay Image</h3>
                                        <div class="prototype-upload-zone" data-upload-zone>
                                            <input type="file" accept="image/png" class="hidden" data-overlay-input>
                                            <div data-overlay-empty class="{{ $selected->hasOverlayImage() ? 'hidden' : '' }}">
                                                <p class="text-sm text-slate-500">Drop a transparent PNG or click to upload.</p>
                                                <p class="text-xs text-slate-400 mt-1">Supports large / 8K PNG files.</p>
                                            </div>
                                            <img data-overlay-preview-img
                                                 src="{{ $selected->overlayUrl() }}"
                                                 alt="Overlay preview"
                                                 class="prototype-overlay-preview {{ $selected->hasOverlayImage() ? '' : 'hidden' }}">
                                            <div class="flex gap-2 mt-3">
                                                <button type="button" data-upload-trigger class="prototype-btn prototype-btn--sm prototype-btn--primary">Upload PNG</button>
                                                <button type="button" data-overlay-delete class="prototype-btn prototype-btn--sm {{ $selected->hasOverlayImage() ? '' : 'hidden' }}">Delete</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="prototype-card p-4 space-y-4" data-section-panel>
                                        <div class="flex items-center justify-between gap-2">
                                            <h3 class="font-semibold">GIS Sections &amp; Slots</h3>
                                            <button type="button" data-draw-cancel class="prototype-btn prototype-btn--sm">Cancel Draw</button>
                                        </div>
                                        <p class="text-xs text-slate-500">Draw colored polygons, rectangles, or plot markers directly on the map.</p>
                                        <p class="text-xs text-amber-500/90">Polygon: click corners, double-click to finish. Rectangle: click &amp; drag. Marker: single click.</p>
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" data-draw-mode="polygon" class="prototype-tool-btn">Draw Polygon</button>
                                            <button type="button" data-draw-mode="rectangle" class="prototype-tool-btn">Draw Rectangle</button>
                                            <button type="button" data-draw-mode="marker" class="prototype-tool-btn">Drop Marker</button>
                                        </div>
                                        <div class="grid grid-cols-3 gap-2">
                                            <label class="prototype-field"><span>Fill</span><input type="color" value="#a9823d" data-draw-style="fill_color"></label>
                                            <label class="prototype-field"><span>Stroke</span><input type="color" value="#6c4815" data-draw-style="stroke_color"></label>
                                            <label class="prototype-field"><span>Fill Opacity</span><input type="range" min="0" max="1" step="0.05" value="0.45" data-draw-style="fill_opacity"></label>
                                        </div>
                                        <div data-section-empty class="text-sm text-slate-500 {{ ($selected->sections ?? collect())->isNotEmpty() ? 'hidden' : '' }}">No sections drawn yet. Pick a draw tool and click on the map.</div>
                                        <div data-section-list class="space-y-2 max-h-40 overflow-y-auto"></div>
                                        <div data-section-form hidden class="space-y-3 pt-2 border-t border-slate-700/50">
                                            <label class="prototype-field"><span>Section Title</span><input type="text" data-section-field="title"></label>
                                            <label class="prototype-field"><span>Map Label</span><input type="text" data-section-field="label" placeholder="Plot 12"></label>
                                            <div class="grid grid-cols-2 gap-2">
                                                <label class="prototype-field"><span>Fill Color</span><input type="color" data-section-field="fill_color"></label>
                                                <label class="prototype-field"><span>Stroke Color</span><input type="color" data-section-field="stroke_color"></label>
                                            </div>
                                            <label class="prototype-field"><span>Fill Opacity</span><input type="range" min="0" max="1" step="0.05" data-section-field="fill_opacity"></label>
                                            <label class="prototype-field">
                                                <span>Status</span>
                                                <select data-section-field="status">
                                                    <option value="active">Active</option>
                                                    <option value="draft">Draft</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </label>
                                            <label class="prototype-field"><span>Notes</span><textarea rows="2" data-section-field="notes" class="w-full rounded-lg border border-slate-600 bg-slate-900 p-2 text-sm"></textarea></label>
                                            <div class="flex gap-2">
                                                <button type="button" data-section-save class="prototype-btn prototype-btn--primary flex-1">Save Section</button>
                                                <button type="button" data-section-delete class="prototype-btn prototype-btn--danger">Delete</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="prototype-card p-4 space-y-4" data-overlay-settings>
                                        <h3 class="font-semibold">Map Configuration</h3>
                                        <label class="prototype-field">
                                            <span>Title</span>
                                            <input type="text" name="title" value="{{ $selected->title }}" data-setting="title">
                                        </label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <label class="prototype-field"><span>North</span><input type="number" step="any" name="north" value="{{ $selected->north }}" data-setting="north"></label>
                                            <label class="prototype-field"><span>South</span><input type="number" step="any" name="south" value="{{ $selected->south }}" data-setting="south"></label>
                                            <label class="prototype-field"><span>East</span><input type="number" step="any" name="east" value="{{ $selected->east }}" data-setting="east"></label>
                                            <label class="prototype-field"><span>West</span><input type="number" step="any" name="west" value="{{ $selected->west }}" data-setting="west"></label>
                                        </div>
                                        <div class="grid grid-cols-3 gap-3">
                                            <label class="prototype-field"><span>Default Zoom</span><input type="number" min="0" max="22" name="default_zoom" value="{{ $selected->default_zoom }}" data-setting="default_zoom"></label>
                                            <label class="prototype-field"><span>Min Zoom</span><input type="number" min="0" max="22" name="min_zoom" value="{{ $selected->min_zoom }}" data-setting="min_zoom"></label>
                                            <label class="prototype-field"><span>Max Zoom</span><input type="number" min="0" max="22" name="max_zoom" value="{{ $selected->max_zoom }}" data-setting="max_zoom"></label>
                                        </div>
                                        <label class="prototype-field">
                                            <span>Overlay Opacity <strong data-opacity-label>{{ round($selected->overlay_opacity * 100) }}%</strong></span>
                                            <input type="range" min="0" max="1" step="0.01" name="overlay_opacity" value="{{ $selected->overlay_opacity }}" data-setting="overlay_opacity">
                                        </label>
                                        <label class="prototype-field">
                                            <span>Show Overlay From Zoom</span>
                                            <input type="number" min="0" max="22" name="show_overlay_from_zoom" value="{{ $selected->show_overlay_from_zoom }}" data-setting="show_overlay_from_zoom" placeholder="Always visible">
                                        </label>
                                        <label class="prototype-field">
                                            <span>Status</span>
                                            <select name="status" data-setting="status">
                                                <option value="draft" @selected($selected->status === 'draft')>Draft</option>
                                                <option value="active" @selected($selected->status === 'active')>Active</option>
                                                <option value="inactive" @selected($selected->status === 'inactive')>Inactive</option>
                                            </select>
                                        </label>
                                        <div class="flex gap-2 pt-2">
                                            <button type="button" data-save-settings class="prototype-btn prototype-btn--primary flex-1">Save</button>
                                            <button type="button" data-reset-settings class="prototype-btn">Reset</button>
                                        </div>
                                        <button type="button" data-delete-overlay class="prototype-btn prototype-btn--danger w-full">Delete Overlay</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="prototype-card p-8 text-center">
                            <p class="text-slate-500">Create your first overlay to begin.</p>
                            <button type="button" id="prototype-create-overlay-empty" class="prototype-btn prototype-btn--primary mt-4">Create Overlay</button>
                        </div>
                    @endif
                </div>
            </section>
        </main>
    </div>

    <script src="{{ asset('theme/js/prototype/MapManager.js') }}"></script>
    <script src="{{ asset('theme/js/prototype/OverlayManager.js') }}"></script>
    <script src="{{ asset('theme/js/prototype/ToolbarManager.js') }}"></script>
    <script src="{{ asset('theme/js/prototype/OverlayUploader.js') }}"></script>
    <script src="{{ asset('theme/js/prototype/OverlaySettings.js') }}"></script>
    <script src="{{ asset('theme/js/prototype/DrawingManager.js') }}"></script>
    <script src="{{ asset('theme/js/prototype/SectionManager.js') }}"></script>
    <script src="{{ asset('theme/js/prototype/SectionPanel.js') }}"></script>
    <script src="{{ asset('theme/js/prototype/prototype-map-editor.js') }}"></script>
</body>
</html>
