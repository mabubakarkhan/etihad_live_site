@php
    $dhaPhases = $dhaPhases ?? collect();
@endphp
<div class="sell-property-form-card" id="sell-property-form-card">
    <form class="sell-property-form" id="sell-property-form" method="post" action="{{ route('sell-rent-lead.store') }}" novalidate>
        @csrf
        <div class="sell-property-form__group">
            <span class="sell-property-form__label">I am looking to</span>
            <div class="sell-property-form__toggle sell-property-form__toggle--2" data-sell-toggle="intent">
                <label class="sell-property-form__pill is-active">
                    <input type="radio" name="intent" value="sell" checked>
                    <span>Sell</span>
                </label>
                <label class="sell-property-form__pill">
                    <input type="radio" name="intent" value="rent">
                    <span>Rent</span>
                </label>
            </div>
        </div>

        <div class="sell-property-form__group sell-property-form__rent-only" hidden>
            <span class="sell-property-form__label">Rent Frequency</span>
            <div class="sell-property-form__toggle sell-property-form__toggle--2" data-sell-toggle="rent_frequency">
                <label class="sell-property-form__pill is-active">
                    <input type="radio" name="rent_frequency" value="yearly" checked>
                    <span>Yearly</span>
                </label>
                <label class="sell-property-form__pill">
                    <input type="radio" name="rent_frequency" value="monthly">
                    <span>Monthly</span>
                </label>
            </div>
        </div>

        <div class="sell-property-form__group">
            <label class="sell-property-form__label" for="sell-location">Location*</label>
            <div class="sell-property-form__input-icon">
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                <input type="text" id="sell-location" name="location" list="sell-dha-phases" placeholder="Enter location, building or DHA phase" required>
            </div>
            @if($dhaPhases->isNotEmpty())
            <datalist id="sell-dha-phases">
                @foreach($dhaPhases as $phase)
                    <option value="{{ $phase->title }}"></option>
                @endforeach
            </datalist>
            @endif
        </div>

        <div class="sell-property-form__group">
            <span class="sell-property-form__label">Category &amp; Type*</span>
            <div class="sell-property-form__toggle sell-property-form__toggle--2 sell-property-form__toggle--wide" data-sell-toggle="category">
                <label class="sell-property-form__pill is-active">
                    <input type="radio" name="category" value="residential" checked>
                    <span>Residential</span>
                </label>
                <label class="sell-property-form__pill">
                    <input type="radio" name="category" value="commercial">
                    <span>Commercial</span>
                </label>
            </div>
            <div class="sell-property-form__chips" data-sell-chips="property_type" data-residential="Apartment,Villa,Townhouse,Penthouse,Land,Other" data-commercial="Office,Shop,Warehouse,Plot,Other">
                @foreach(['Apartment', 'Villa', 'Townhouse', 'Penthouse', 'Land', 'Other'] as $type)
                    <label class="sell-property-form__chip">
                        <input type="radio" name="property_type" value="{{ $type }}" {{ $loop->first ? 'checked' : '' }}>
                        <span>{{ $type }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="sell-property-form__group sell-property-form__beds-group">
            <span class="sell-property-form__label">Bedrooms*</span>
            <div class="sell-property-form__chips" data-sell-chips="bedrooms">
                @foreach(['Studio', '1', '2', '3', '4', '5', '6', '7', '8+'] as $bed)
                    <label class="sell-property-form__chip">
                        <input type="radio" name="bedrooms" value="{{ $bed }}" {{ $bed === '2' ? 'checked' : '' }}>
                        <span>{{ $bed }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="sell-property-form__row">
            <div class="sell-property-form__group">
                <label class="sell-property-form__label" for="sell-area">Area (sqft)</label>
                <input type="text" id="sell-area" name="area_sqft" placeholder="Enter area" class="sell-property-form__input">
            </div>
            <div class="sell-property-form__group">
                <label class="sell-property-form__label" for="sell-furnishing">Furnishing</label>
                <select id="sell-furnishing" name="furnishing" class="sell-property-form__input">
                    <option value="">Select</option>
                    <option value="furnished">Furnished</option>
                    <option value="semi-furnished">Semi-furnished</option>
                    <option value="unfurnished">Unfurnished</option>
                </select>
            </div>
        </div>

        <div class="sell-property-form__group">
            <span class="sell-property-form__label">Urgency</span>
            <div class="sell-property-form__chips" data-sell-chips="urgency">
                @foreach(['This month' => 'this_month', 'Within 2 months' => 'within_2_months', 'Flexible' => 'flexible'] as $label => $val)
                    <label class="sell-property-form__chip">
                        <input type="radio" name="urgency" value="{{ $val }}" {{ $val === 'flexible' ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="sell-property-form__row sell-property-form__row--contact">
            <div class="sell-property-form__group">
                <label class="sell-property-form__label" for="sell-name">Your Name*</label>
                <input type="text" id="sell-name" name="name" class="sell-property-form__input" required>
            </div>
            <div class="sell-property-form__group">
                <label class="sell-property-form__label" for="sell-phone">Phone*</label>
                <input type="text" id="sell-phone" name="phone" class="sell-property-form__input" required>
            </div>
            <div class="sell-property-form__group">
                <label class="sell-property-form__label" for="sell-email">Email</label>
                <input type="email" id="sell-email" name="email" class="sell-property-form__input">
            </div>
        </div>

        <div class="sell-property-form__msg" id="sell-property-form-msg" aria-live="polite"></div>

        <button type="submit" class="sell-property-form__submit" id="sell-property-form-submit">
            <span class="sell-property-form__submit-text">{{ $formSubmitLabel ?? 'Continue' }}</span>
            <span class="sell-property-form__submit-loading" hidden><i class="fa-solid fa-spinner fa-spin"></i> Submitting…</span>
        </button>
    </form>
</div>
