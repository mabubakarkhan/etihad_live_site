@foreach($testimonials as $testimonial)
@php
    $imageUrl = $testimonial->image
        ? url('storage/' . ltrim($testimonial->image, '/'))
        : asset('theme/images/avatar/1.jpg');
    $cardId = 'card' . $loop->iteration;
@endphp
                <div class="reviews-swiper-slide">
                  <span class="reviews-swiper-slide__quote">
                    <svg xmlns="http://www.w3.org/2000/svg" width="33" height="26" viewBox="0 0 33 26" fill="none">
                      <path
                        d="M9.45801 17.5L10.458 13.5L14.458 10L14.958 12L14.458 15L13.458 17.5L8.95801 22.5L5.45801 24.5L4.45801 24L5.95801 22.5L9.45801 17.5Z"
                        fill="#bb9c46"
                      />
                      <path
                        d="M10.458 13.5C10.2913 15.8333 8.55801 21.4 2.95801 25C8.12467 23.6667 17.658 18.8 14.458 6"
                        stroke="#bb9c46"
                      />
                      <circle cx="7.95801" cy="7.5" r="7.5" fill="#bb9c46" />
                      <path
                        d="M26.458 17.5L27.458 13.5L31.458 10L31.958 12L31.458 15L30.458 17.5L25.958 22.5L22.458 24.5L21.458 24L22.958 22.5L26.458 17.5Z"
                        fill="#bb9c46"
                      />
                      <path
                        d="M27.458 13.5C27.2913 15.8333 25.558 21.4 19.958 25C25.1247 23.6667 34.658 18.8 31.458 6"
                        stroke="#bb9c46"
                      />
                      <circle cx="24.958" cy="7.5" r="7.5" fill="#bb9c46" />
                    </svg>
                  </span>

                  <p class="reviews-swiper-slide__text">{{ $testimonial->comment }}</p>

                  <div class="reviews-swiper-slide__user">
                    <img
                      src="{{ $imageUrl }}"
                      alt="{{ $testimonial->name }} review photo"
                      class="reviews-swiper-slide__user-avatar"
                      loading="lazy"
                    />

                    <div class="reviews-swiper-slide__user-info">
                      <strong class="reviews-swiper-slide__user-info-name">{{ $testimonial->name }}</strong>
                      @if($testimonial->city)
                      <span class="reviews-swiper-slide__user-info-job">{{ $testimonial->city }}</span>
                      @endif
                    </div>

                    <div class="reviews-swiper-slide__user-rating">
                      <input type="radio" id="{{ $cardId }}-5-stars" name="{{ $cardId }}-rating" value="5" checked />
                      <label for="{{ $cardId }}-5-stars" class="star">&#9733;</label>

                      <input type="radio" id="{{ $cardId }}-4-stars" name="{{ $cardId }}-rating" value="4" />
                      <label for="{{ $cardId }}-4-stars" class="star">&#9733;</label>

                      <input type="radio" id="{{ $cardId }}-3-stars" name="{{ $cardId }}-rating" value="3" />
                      <label for="{{ $cardId }}-3-stars" class="star">&#9733;</label>

                      <input type="radio" id="{{ $cardId }}-2-stars" name="{{ $cardId }}-rating" value="2" />
                      <label for="{{ $cardId }}-2-stars" class="star">&#9733;</label>

                      <input type="radio" id="{{ $cardId }}-1-star" name="{{ $cardId }}-rating" value="1" />
                      <label for="{{ $cardId }}-1-star" class="star">&#9733;</label>
                    </div>
                  </div>

                  <div class="reviews-swiper-slide__line">
                  </div>
                </div>
@endforeach
