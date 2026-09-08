@php
    $editorId = 'interactive-map-editor-' . $ownerType . '-' . $ownerId;
    $mapPayload = isset($map) ? $map->toEditorArray($ownerType, $ownerId) : null;
    $googleMapsKey = config('app.google_maps_api_key', '');
    $googleMapsMapId = config('app.google_maps_map_id', 'DEMO_MAP_ID');
    $apiBase = url('/admin/interactive-map/' . $ownerType . '/' . $ownerId);
@endphp

<div
    id="{{ $editorId }}"
    class="interactive-map-editor"
    data-owner-type="{{ $ownerType }}"
    data-owner-id="{{ $ownerId }}"
    data-api-base="{{ $apiBase }}"
    data-csrf="{{ csrf_token() }}"
    data-google-maps-key="{{ $googleMapsKey }}"
    data-google-maps-map-id="{{ $googleMapsMapId }}"
    data-initial='@json($mapPayload ?? new \stdClass())'
>
    <div class="interactive-map-editor__header">
        <div>
            <h3 class="interactive-map-editor__title">Phase map</h3>
            <p class="interactive-map-editor__lead"><strong>Gold box</strong> = map area. Drag the box or its corners to move/resize. Then upload a PNG to fill that area. Draw plots after.</p>
        </div>
        @if(!empty($standaloneUrl))
            <a href="{{ $standaloneUrl }}" class="interactive-map-editor__standalone-link" target="_blank" rel="noopener">Open full editor</a>
        @endif
    </div>

    <div class="interactive-map-editor__layout">
        <aside class="interactive-map-editor__sidebar">
            <div class="interactive-map-editor__panel">
                <h4 class="interactive-map-editor__panel-title">1. Gold box (map area)</h4>
                <p class="interactive-map-editor__hint" style="margin-top:0">On the map: drag the gold square to move it. Drag the corner handles to resize. It saves when you release.</p>
                <div class="interactive-map-editor__btn-row interactive-map-editor__btn-row--wrap">
                    <button type="button" class="interactive-map-editor__btn interactive-map-editor__btn--primary is-active" data-overlay-edit-mode>Move / resize box</button>
                    <button type="button" class="interactive-map-editor__btn" data-plot-edit-mode>Draw plots</button>
                </div>
            </div>

            <div class="interactive-map-editor__panel">
                <h4 class="interactive-map-editor__panel-title">2. Overlay image (fills the gold box)</h4>
                <div class="interactive-map-editor__overlay-preview-wrap" data-overlay-preview-wrap>
                    <img src="" alt="Overlay preview" class="interactive-map-editor__overlay-preview hidden" data-overlay-preview-img />
                    <p class="interactive-map-editor__empty-hint" data-overlay-empty>No image yet — upload a PNG/SVG master plan.</p>
                </div>
                <label class="interactive-map-editor__file-label">
                    <span>Upload / replace PNG or SVG</span>
                    <input type="file" accept="image/png,image/svg+xml,.svg" data-overlay-input class="interactive-map-editor__file-input" />
                </label>
                <div class="interactive-map-editor__btn-row">
                    <button type="button" class="interactive-map-editor__btn interactive-map-editor__btn--danger" data-overlay-delete disabled>Delete image</button>
                </div>
            </div>

            <div class="interactive-map-editor__panel">
                <h4 class="interactive-map-editor__panel-title">Settings</h4>
                <div class="interactive-map-editor__grid interactive-map-editor__grid--2" hidden data-bounds-fields>
                    <label class="interactive-map-editor__field">
                        <span>North</span>
                        <input type="number" step="any" data-field="north" class="interactive-map-editor__input" />
                    </label>
                    <label class="interactive-map-editor__field">
                        <span>South</span>
                        <input type="number" step="any" data-field="south" class="interactive-map-editor__input" />
                    </label>
                    <label class="interactive-map-editor__field">
                        <span>East</span>
                        <input type="number" step="any" data-field="east" class="interactive-map-editor__input" />
                    </label>
                    <label class="interactive-map-editor__field">
                        <span>West</span>
                        <input type="number" step="any" data-field="west" class="interactive-map-editor__input" />
                    </label>
                </div>
                <div class="interactive-map-editor__grid interactive-map-editor__grid--3">
                    <label class="interactive-map-editor__field">
                        <span>Default zoom</span>
                        <input type="number" min="0" max="22" data-field="default_zoom" class="interactive-map-editor__input" />
                    </label>
                    <label class="interactive-map-editor__field">
                        <span>Min zoom</span>
                        <input type="number" min="0" max="22" data-field="min_zoom" class="interactive-map-editor__input" />
                    </label>
                    <label class="interactive-map-editor__field">
                        <span>Max zoom</span>
                        <input type="number" min="0" max="22" data-field="max_zoom" class="interactive-map-editor__input" />
                    </label>
                </div>
                <div class="interactive-map-editor__grid interactive-map-editor__grid--2">
                    <label class="interactive-map-editor__field">
                        <span>Image opacity</span>
                        <input type="number" min="0" max="1" step="0.01" data-field="overlay_opacity" class="interactive-map-editor__input" />
                    </label>
                    <label class="interactive-map-editor__field">
                        <span>Plot titles from zoom</span>
                        <input type="number" min="0" max="22" data-field="show_label_from_zoom" class="interactive-map-editor__input" placeholder="16" />
                    </label>
                </div>
                <input type="hidden" data-field="overlay_visibility_zoom" />
                <label class="interactive-map-editor__checkbox">
                    <input type="checkbox" data-field="is_active" />
                    <span>Show on website</span>
                </label>
                <div class="interactive-map-editor__btn-row">
                    <button type="button" class="interactive-map-editor__btn interactive-map-editor__btn--primary" data-save-settings>Save</button>
                </div>
            </div>

            <div class="interactive-map-editor__panel">
                <div class="interactive-map-editor__panel-head">
                    <h4 class="interactive-map-editor__panel-title">3. Plots</h4>
                </div>
                <p class="interactive-map-editor__hint">Click a plot to edit/delete it. Or open the plot tools panel to draw a new one.</p>
                <div class="interactive-map-editor__btn-row">
                    <button type="button" class="interactive-map-editor__btn interactive-map-editor__btn--primary" data-open-plot-drawer>Add / edit plot</button>
                </div>
                <div data-section-empty class="interactive-map-editor__empty-hint">No plots yet.</div>
                <div data-section-list class="interactive-map-editor__section-list"></div>
            </div>
        </aside>

        <div class="interactive-map-editor__map-wrap">
            <div class="interactive-map-editor__toolbar" data-toolbar>
                <div class="interactive-map-editor__search-wrap">
                    <label class="interactive-map-editor__search-label" for="{{ $editorId }}-search">Search location</label>
                    <input
                        type="text"
                        id="{{ $editorId }}-search"
                        class="interactive-map-editor__search-input"
                        data-map-search
                        placeholder="Search landmark…"
                        autocomplete="off"
                    />
                </div>
                <span class="interactive-map-editor__status" data-status>Loading map…</span>
            </div>
            <div id="{{ $editorId }}-draw-hint" class="interactive-map-editor__draw-hint" hidden data-draw-hint></div>
            <div class="interactive-map-editor__map" data-map-canvas></div>

            <aside class="interactive-map-plot-drawer" data-plot-drawer hidden>
                <div class="interactive-map-plot-drawer__head">
                    <div>
                        <p class="interactive-map-plot-drawer__eyebrow">Plot tools</p>
                        <h4 class="interactive-map-plot-drawer__title" data-plot-drawer-title>Add or edit plot</h4>
                    </div>
                    <button type="button" class="interactive-map-editor__btn interactive-map-editor__btn--sm" data-close-plot-drawer>Close</button>
                </div>

                <div class="interactive-map-plot-drawer__section">
                    <p class="interactive-map-editor__hint" style="margin:0">1. Pick a shape, then click the map.</p>
                    <div class="interactive-map-editor__btn-row interactive-map-editor__btn-row--wrap">
                        <button type="button" class="interactive-map-editor__tool-btn" data-draw-mode="polygon">Polygon</button>
                        <button type="button" class="interactive-map-editor__tool-btn" data-draw-mode="rectangle">Rectangle</button>
                        <button type="button" class="interactive-map-editor__tool-btn" data-draw-mode="marker">Marker</button>
                    </div>
                    <div class="interactive-map-editor__btn-row interactive-map-editor__btn-row--wrap">
                        <button type="button" class="interactive-map-editor__btn" data-draw-undo>Undo last point</button>
                        <button type="button" class="interactive-map-editor__btn interactive-map-editor__btn--primary" data-draw-finish>Finish shape</button>
                        <button type="button" class="interactive-map-editor__btn" data-draw-cancel>Cancel draw</button>
                    </div>
                    <div class="interactive-map-editor__grid interactive-map-editor__grid--3">
                        <label class="interactive-map-editor__field"><span>Fill</span><input type="color" value="#a9823d" data-draw-style="fill_color" class="interactive-map-editor__input"></label>
                        <label class="interactive-map-editor__field"><span>Stroke</span><input type="color" value="#6c4815" data-draw-style="stroke_color" class="interactive-map-editor__input"></label>
                        <label class="interactive-map-editor__field"><span>Opacity</span><input type="range" min="0" max="1" step="0.05" value="0.45" data-draw-style="fill_opacity" class="interactive-map-editor__input"></label>
                    </div>
                </div>

                <div data-section-form class="interactive-map-plot-drawer__section interactive-map-editor__section-form" hidden>
                    <p class="interactive-map-editor__hint" style="margin:0">2. Selected plot — drag corners on map to fix shape, or delete it.</p>
                    <label class="interactive-map-editor__field"><span>Title</span><input type="text" data-section-field="title" class="interactive-map-editor__input"></label>
                    <label class="interactive-map-editor__field"><span>Map label</span><input type="text" data-section-field="label" class="interactive-map-editor__input" placeholder="Plot 12"></label>
                    <label class="interactive-map-editor__field"><span>Show title from zoom</span><input type="number" min="0" max="22" data-section-field="show_label_from_zoom" class="interactive-map-editor__input" placeholder="Map default"></label>
                    <div class="interactive-map-editor__grid interactive-map-editor__grid--2">
                        <label class="interactive-map-editor__field"><span>Fill</span><input type="color" data-section-field="fill_color" class="interactive-map-editor__input"></label>
                        <label class="interactive-map-editor__field"><span>Stroke</span><input type="color" data-section-field="stroke_color" class="interactive-map-editor__input"></label>
                    </div>
                    <label class="interactive-map-editor__field"><span>Fill opacity</span><input type="range" min="0" max="1" step="0.05" data-section-field="fill_opacity" class="interactive-map-editor__input"></label>
                    <label class="interactive-map-editor__field">
                        <span>Status</span>
                        <select data-section-field="status" class="interactive-map-editor__input">
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </label>
                    <label class="interactive-map-editor__field"><span>Notes</span><textarea rows="2" data-section-field="notes" class="interactive-map-editor__input"></textarea></label>
                    <div class="interactive-map-editor__btn-row interactive-map-editor__btn-row--wrap">
                        <button type="button" class="interactive-map-editor__btn interactive-map-editor__btn--primary" data-section-save>Save plot</button>
                        <button type="button" class="interactive-map-editor__btn interactive-map-editor__btn--danger" data-section-delete>Delete plot</button>
                        <button type="button" class="interactive-map-editor__btn" data-section-clear-focus>Done</button>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <div class="interactive-map-editor__toast hidden" data-toast role="status"></div>
</div>

@include('admin.interactive-map._assets')
