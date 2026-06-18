@php $hideListingFilters = $hideListingFilters ?? false; @endphp
<div class="listing-search-block-root{{ $hideListingFilters ? ' listing-page-no-filters' : '' }}">
                        <div class="listing-filters-section{{ $hideListingFilters ? ' listing-filters-section--hidden' : '' }}">
                        <div class="show-mob-filter"><i class="far fa-sliders-h"></i> Search listings</div>

                        {{-- Filters (hidden on listing + DHA phase pages when $hideListingFilters) --}}
                        <div class="list-searh-input-wrap box_list-searh-input-wrap lws_mobile lsw_mb-btn">
                            <div class="close_mob-filter cmf"><i class="fa-regular fa-xmark"></i></div>
                            <div class="list-searh-input-wrap-title_wrap">
                                <div class="list-searh-input-wrap-title"><i class="far fa-sliders-h"></i><span>Search listings</span></div>
                                <div class="list-searh-input-radio_wrap">
                                    <div class="header-search-radio">
                                        <input class="hidden radio-label" type="radio" name="listing_purpose" id="sale-button2" value="sale" checked="checked">
                                        <label class="button-label" for="sale-button2">Sale</label>
                                        <input class="hidden radio-label" type="radio" name="listing_purpose" id="rent-button2" value="rent">
                                        <label class="button-label" for="rent-button2">Rent</label>
                                    </div>
                                    <div class="reset-form reset-btn tolt" data-microtip-position="bottom" data-tooltip="Reset Filters" id="listing-reset-filters"><i class="fa-solid fa-arrows-rotate"></i></div>
                                </div>
                            </div>
                            <div class="custom-form">
                                <input type="hidden" id="listing-filter-property-type" value="">
                                <input type="hidden" id="listing-default-city-id" value="{{ $lahoreCityId ?? '' }}" data-default="{{ $lahoreCityId ?? '' }}" data-city-name="Lahore">
                                <div class="row g-3 align-items-center listing-filter-main-row">
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <div class="cs-intputwrap listing-landmark-wrap">
                                            <i class="fa-light fa-location-dot"></i>
                                            <input type="text" id="listing-location-a" class="listing-filter" placeholder="Location A (landmark/place)" autocomplete="off">
                                            <input type="hidden" id="listing-location-a-lat" value="">
                                            <input type="hidden" id="listing-location-a-lng" value="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <div class="cs-intputwrap listing-landmark-wrap">
                                            <i class="fa-light fa-location-dot"></i>
                                            <input type="text" id="listing-location-b" class="listing-filter" placeholder="Location B (landmark/place)" autocomplete="off">
                                            <input type="hidden" id="listing-location-b-lat" value="">
                                            <input type="hidden" id="listing-location-b-lng" value="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <div class="cs-intputwrap">
                                            <i class="fa-light fa-map"></i>
                                            <select id="listing-dha-phase" name="listing_dha_phase" data-placeholder="All DHA Phases" class="chosen-select on-radius no-search-select listing-filter">
                                                <option value="">All DHA Phases</option>
                                                @foreach($dhaPhases ?? [] as $dp)
                                                    <option value="{{ $dp->id }}" @selected(isset($defaultDhaPhaseId) && (string) $defaultDhaPhaseId === (string) $dp->id)>{{ $dp->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <div class="cs-intputwrap">
                                            <i class="fa-light fa-layer-group"></i>
                                            <select id="listing-project-type" name="listing_project_type" data-placeholder="All Categories" class="chosen-select on-radius no-search-select listing-filter">
                                                <option value="">All Categories</option>
                                                @foreach($projectTypes ?? [] as $pt)
                                                    <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <div class="cs-intputwrap listing-range-dropdown-cswrap">
                                            <i class="fa-light fa-ruler-combined" aria-hidden="true"></i>
                                            <div class="listing-range-dropdown">
                                                <button type="button" class="listing-range-dropdown-btn" id="listing-area-range-toggle" aria-expanded="false" aria-controls="listing-area-range-panel">
                                                    <span class="listing-range-dropdown-title">Area</span>
                                                    <span class="listing-range-dropdown-summary" id="listing-area-range-summary"></span>
                                                    <i class="fa-solid fa-chevron-down listing-range-dropdown-caret" aria-hidden="true"></i>
                                                </button>
                                                <div class="listing-range-dropdown-panel" id="listing-area-range-panel" role="region" aria-label="Area range">
                                                    <div class="listing-range-dropdown-panel-inner">
                                                        <div class="price-range-wrap fl-wrap listing-range-wrap">
                                                            <label>Area/Marla</label>
                                                            <div class="price-rage-item pr-nopad fl-wrap">
                                                                <input type="text" id="listing-area-range" class="price-range-double listing-filter listing-deferred-range" data-min="1" data-max="400" data-from="1" data-to="20" name="area_range" data-step="1" data-prefix="" autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-2">
                                        <button type="button" id="listing-search-btn" class="commentssubmit listing-filter-search-btn w-100">Search</button>
                                    </div>
                                    <div class="d-none">
                                        <div class="cs-intputwrap listing-address-wrap">
                                            <i class="fa-light fa-location-dot"></i>
                                            <input type="text" id="listing-address" name="listing_address" class="listing-filter" placeholder="Address, street, area…" value="" autocomplete="off">
                                            <input type="hidden" id="listing-address-value" name="listing_address_value" value="">
                                            <div id="listing-address-suggestions" class="listing-address-suggestions" role="listbox" aria-hidden="true"></div>
                                        </div>
                                    </div>
                                    <div class="d-none">
                                        <div class="hidden-listing_search_wrap fl-wrap listing-actions-wrap listing-filter-actions-row">
                                            <div class="more_search-btn listing-more-btn" id="listing-more-options">More Options <i class="fa-regular fa-plus"></i><span class="more-options-dot" id="more-options-dot" aria-hidden="true"></span></div>
                                            <div class="hidden-listing-filter etihad-is-hidden">
                                                <div class="quantity_wrap">
                                                    <div class="quantity_wrap_title"><i class="fa-light fa-bed"></i><span>Bedrooms</span></div>
                                                    <div class="quantity">
                                                        <div class="quantity-item">
                                                            <input type="button" value="-" class="minus">
                                                            <input type="text" name="listing_bedrooms" id="listing_bedrooms" title="Qty" class="qty" data-min="0" data-max="20" data-step="1" value="0">
                                                            <input type="button" value="+" class="plus">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="quantity_wrap">
                                                    <div class="quantity_wrap_title"><i class="fa-light fa-bath"></i><span>Bathrooms</span></div>
                                                    <div class="quantity">
                                                        <div class="quantity-item">
                                                            <input type="button" value="-" class="minus">
                                                            <input type="text" name="listing_bathrooms" id="listing_bathrooms" title="Qty" class="qty" data-min="0" data-max="20" data-step="1" value="0">
                                                            <input type="button" value="+" class="plus">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="quantity_wrap">
                                                    <div class="quantity_wrap_title"><i class="fa-light fa-utensils"></i><span>Kitchen</span></div>
                                                    <div class="quantity">
                                                        <div class="quantity-item">
                                                            <input type="button" value="-" class="minus">
                                                            <input type="text" name="listing_kitchen" id="listing_kitchen" title="Qty" class="qty" data-min="0" data-max="20" data-step="1" value="0">
                                                            <input type="button" value="+" class="plus">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="hidden-listing-item">
                                                    <div class="filter-tags-title">Amenities</div>
                                                    <div class="filter-tags">
                                                        <ul class="no-list-style">
                                                            <li><input id="check-aa" type="checkbox" name="listing_amenity[]" value="elevator"><label for="check-aa">Elevator in building</label></li>
                                                            <li><input id="check-b" type="checkbox" name="listing_amenity[]" value="laundry"><label for="check-b">Laundry Room</label></li>
                                                            <li><input id="check-c" type="checkbox" name="listing_amenity[]" value="kitchen"><label for="check-c">Equipped Kitchen</label></li>
                                                            <li><input id="check-d" type="checkbox" name="listing_amenity[]" value="ac"><label for="check-d">Air Conditioned</label></li>
                                                            <li><input id="check-d2" type="checkbox" name="listing_amenity[]" value="parking"><label for="check-d2">Parking</label></li>
                                                            <li><input id="check-d3" type="checkbox" name="listing_amenity[]" value="pool"><label for="check-d3">Swimming Pool</label></li>
                                                            <li><input id="check-d4" type="checkbox" name="listing_amenity[]" value="gym"><label for="check-d4">Fitness Gym</label></li>
                                                            <li><input id="check-d5" type="checkbox" name="listing_amenity[]" value="security"><label for="check-d5">Security</label></li>
                                                            <li><input id="check-d6" type="checkbox" name="listing_amenity[]" value="garage"><label for="check-d6">Garage Attached</label></li>
                                                            <li><input id="check-d7" type="checkbox" name="listing_amenity[]" value="backyard"><label for="check-d7">Back yard</label></li>
                                                            <li><input id="check-d8" type="checkbox" name="listing_amenity[]" value="fireplace"><label for="check-d8">Fireplace</label></li>
                                                            <li><input id="check-d9" type="checkbox" name="listing_amenity[]" value="window_covering"><label for="check-d9">Window Covering</label></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mob-filter-overlay cmf fs-wrapper"></div>
                        </div>{{-- /.listing-filters-section --}}

                        {{-- Results header (count updated via JS) --}}
                        <div class="list-main-wrap-header box-list-header">
                            <div class="list-main-wrap-title">
                                <h2>Results: <span id="listing-results-label">{{ $listingResultsLabel ?? 'Listings' }}</span> <strong id="listing-count">0</strong></h2>
                            </div>
                            <div class="list-main-wrap-opt">
                                <div class="price-opt">
                                    <span class="price-opt-title">Sort by:</span>
                                    <div class="cs-intputwrap listing-search-submit-wrap">
                                        <i class="fa-light fa-arrow-down-small-big"></i>
                                        <select id="listing-sort" name="listing_sort" data-placeholder="Latest" class="chosen-select no-search-select listing-filter">
                                            <option value="latest" selected>Latest</option>
                                            <option value="price_asc">Price: low to high</option>
                                            <option value="price_desc">Price: high to low</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Container: sidebar + listing area for better management --}}
                        <div class="listing-area-container">
                            <div class="row listing-page-row">
                                <aside class="col-lg-6 col-xl-6 order-2 order-lg-1 listing-sidebar d-none d-lg-block">
                                    <div class="listing-sidebar-inner">
                                        <div id="listing-sidebar-map" class="listing-sidebar-map" aria-label="Map of listed properties"></div>
                                    </div>
                                </aside>
                                <div class="col-lg-6 col-xl-6 order-1 order-lg-2 listing-main">
                                    <div class="listing-area-wrap">
                                        <div id="listing-loader" class="listing-inline-loader">
                                            <div class="loader-inner">
                                                <svg>
                                                    <defs>
                                                        <filter id="goo">
                                                            <fegaussianblur in="SourceGraphic" stdDeviation="2" result="blur" />
                                                            <fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 5 -2" result="gooey" />
                                                            <fecomposite in="SourceGraphic" in2="gooey" operator="atop"/>
                                                        </filter>
                                                    </defs>
                                                </svg>
                                            </div>
                                        </div>
                                        <div id="listing-empty" class="etihad-is-hidden">
                                            <p>No listings found.</p>
                                        </div>
                                        <div id="listing-grid" class="listing-item-container three-columns-grid etihad-is-hidden">
                                            {{-- Filled via AJAX --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="listing-pagination-wrap" class="pagination-wrap etihad-is-hidden"></div>
</div>{{-- /.listing-search-block-root --}}
