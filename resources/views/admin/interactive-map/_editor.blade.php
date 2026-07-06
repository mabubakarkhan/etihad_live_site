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
    data-initial="{{ $mapPayload ? json_encode($mapPayload) : '{}' }}"
>
    <div class="interactive-map-editor__header">
        <div>
            <h3 class="interactive-map-editor__title">Google Maps ground overlay</h3>
            <p class="interactive-map-editor__lead">Upload a transparent PNG or SVG master plan, then drag it on the map to position it. Property markers remain independent.</p>
        </div>
        @if(!empty($standaloneUrl))
            <a href="{{ $standaloneUrl }}" class="interactive-map-editor__standalone-link" target="_blank" rel="noopener">Open full editor</a>
        @endif
    </div>

    <div class="interactive-map-editor__layout">
        <aside class="interactive-map-editor__sidebar">
            <div class="interactive-map-editor__panel">
                <h4 class="interactive-map-editor__panel-title">Overlay image</h4>
                <div class="interactive-map-editor__overlay-preview-wrap" data-overlay-preview-wrap>
                    <img src="" alt="Overlay preview" class="interactive-map-editor__overlay-preview hidden" data-overlay-preview-img />
                    <p class="interactive-map-editor__empty-hint" data-overlay-empty>No overlay uploaded yet.</p>
                </div>
                <label class="interactive-map-editor__file-label">
                    <span>Upload PNG or SVG</span>
                    <input type="file" accept="image/png,image/svg+xml,.svg" data-overlay-input class="interactive-map-editor__file-input" />
                </label>
                <div class="interactive-map-editor__btn-row">
                    <button type="button" class="interactive-map-editor__btn interactive-map-editor__btn--danger" data-overlay-delete disabled>Delete overlay</button>
                </div>
                <p class="interactive-map-editor__hint">After upload, drag the gold overlay area on the map. Position saves automatically when you release the mouse.</p>
            </div>

            <div class="interactive-map-editor__panel">
                <h4 class="interactive-map-editor__panel-title">Bounds &amp; zoom</h4>
                <div class="interactive-map-editor__grid interactive-map-editor__grid--2">
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
                        <span>Overlay opacity</span>
                        <input type="number" min="0" max="1" step="0.01" data-field="overlay_opacity" class="interactive-map-editor__input" />
                    </label>
                    <label class="interactive-map-editor__field">
                        <span>Visibility zoom</span>
                        <input type="number" min="0" max="22" data-field="overlay_visibility_zoom" class="interactive-map-editor__input" />
                    </label>
                </div>
                <label class="interactive-map-editor__checkbox">
                    <input type="checkbox" data-field="is_active" />
                    <span>Active (show overlay on front-end maps)</span>
                </label>
                <div class="interactive-map-editor__btn-row">
                    <button type="button" class="interactive-map-editor__btn interactive-map-editor__btn--primary" data-save-settings>Save settings</button>
                </div>
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
                        placeholder="Landmark / place (same search as listing Place A & B)"
                        autocomplete="off"
                    />
                </div>
                <span class="interactive-map-editor__status" data-status>Loading map…</span>
            </div>
            <div class="interactive-map-editor__map" data-map-canvas></div>
        </div>
    </div>

    <div class="interactive-map-editor__toast hidden" data-toast role="status"></div>
</div>

@include('admin.interactive-map._assets')
