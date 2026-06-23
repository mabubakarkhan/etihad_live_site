              <div class="popular-listings__panel" id="hot-offers-popular" role="tabpanel" aria-labelledby="hot-offers-tab-popular">
                <div class="popular-listings__rail">
                <div class="popular-listings__grid">
@foreach($popularProperties as $property)
@include('partials.homepage-hot-offers-card', ['property' => $property, 'badge' => $property->is_hot ? 'Featured' : null])
@endforeach
                </div>
                </div>
              </div>

              <div class="popular-listings__panel" id="hot-offers-residential" role="tabpanel" aria-labelledby="hot-offers-tab-residential" hidden>
                <div class="popular-listings__rail">
                <div class="popular-listings__grid">
@foreach($residentialProperties as $property)
@include('partials.homepage-hot-offers-card', ['property' => $property, 'badge' => 'Residential'])
@endforeach
                </div>
                </div>
              </div>

              <div class="popular-listings__panel" id="hot-offers-commercial" role="tabpanel" aria-labelledby="hot-offers-tab-commercial" hidden>
                <div class="popular-listings__rail">
                <div class="popular-listings__grid">
@foreach($commercialProperties as $property)
@include('partials.homepage-hot-offers-card', ['property' => $property, 'badge' => 'Commercial'])
@endforeach
                </div>
                </div>
              </div>
