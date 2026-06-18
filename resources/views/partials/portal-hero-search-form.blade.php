{{-- Portal homepage quick search (hero modal) — form kept for future reuse --}}
<div class="list-searh-input-wrap box_list-searh-input-wrap lws_column lsiw_dec" id="portal-quick-search-wrap" data-listing-url="{{ route('listing') }}">
    <div class="list-searh-input-wrap-title_wrap portal-home-hero-search-toolbar">
        <div class="header-search-radio">
            <input class="hidden radio-label" type="radio" name="portalPurpose" id="portal-purpose-sale" value="sale" checked="checked">
            <label class="button-label" for="portal-purpose-sale">Buy</label>
            <input class="hidden radio-label" type="radio" name="portalPurpose" id="portal-purpose-rent" value="rent">
            <label class="button-label" for="portal-purpose-rent">Rent</label>
        </div>
        <div class="portal-home-hero-toolbar-actions">
            <span class="portal-home-hero-filter-icon" aria-hidden="true"><i class="far fa-sliders-h"></i></span>
            <button type="button" class="reset-form reset-btn tolt" id="portal-quick-search-reset" data-microtip-position="bottom" data-tooltip="Reset Filters" aria-label="Reset filters"><i class="fa-solid fa-arrows-rotate"></i></button>
        </div>
    </div>
    <div class="custom-form">
        <input type="hidden" id="portal-default-city-id" value="{{ $lahoreCityId ?? '' }}" data-default="{{ $lahoreCityId ?? '' }}" data-city-name="Lahore" autocomplete="off">
        <div class="row g-2 portal-home-hero-hidden-fields">
            <div class="col-12">
                <div class="cs-intputwrap">
                    <i class="fa-light fa-layer-group"></i>
                    <select id="portal-project-type" data-placeholder="All Categories" class="chosen-select on-radius no-search-select">
                        <option value="">All Categories</option>
                        @foreach(($projectTypes ?? []) as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12">
                <div class="cs-intputwrap listing-range-dropdown-cswrap">
                    <i class="fa-light fa-ruler-combined" aria-hidden="true"></i>
                    <div class="listing-range-dropdown">
                        <button type="button" class="listing-range-dropdown-btn" id="portal-area-range-toggle" aria-expanded="false" aria-controls="portal-area-range-panel">
                            <span class="listing-range-dropdown-title">Area</span>
                            <span class="listing-range-dropdown-summary" id="portal-area-range-summary"></span>
                            <i class="fa-solid fa-chevron-down listing-range-dropdown-caret" aria-hidden="true"></i>
                        </button>
                        <div class="listing-range-dropdown-panel" id="portal-area-range-panel" role="region" aria-label="Area range">
                            <div class="listing-range-dropdown-panel-inner">
                                <div class="price-range-wrap fl-wrap listing-range-wrap">
                                    <label>Area/Marla</label>
                                    <div class="price-rage-item pr-nopad fl-wrap">
                                        <input type="text" id="portal-area-range" class="price-range-double listing-deferred-range" data-min="1" data-max="400" data-from="1" data-to="20" name="portal_area_range" data-step="1" data-prefix="" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="portal-home-hero-search-inline">
            <div class="cs-intputwrap listing-address-wrap portal-home-hero-address-field">
                <i class="fa-light fa-location-dot"></i>
                <input type="text" id="portal-quick-address" placeholder="Address, street, area…" value="" autocomplete="off">
                <input type="hidden" id="portal-quick-address-value" value="">
                <div id="portal-address-suggestions" class="listing-address-suggestions" role="listbox" aria-hidden="true"></div>
            </div>
            <button type="button" id="portal-quick-search-btn" class="commentssubmit portal-home-hero-search-btn">Search</button>
        </div>
    </div>
</div>
