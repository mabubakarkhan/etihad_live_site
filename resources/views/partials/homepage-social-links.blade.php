@php
    $cs = $cs ?? \App\Models\ContactSetting::instance();
    $linkClass = $linkClass ?? 'menu-socials__link';
    $networks = [
        'facebook' => $cs->facebook ?? null,
        'instagram' => $cs->instagram ?? null,
        'linkedin' => $cs->linkedin ?? null,
        'youtube' => $cs->youtube ?? null,
        'twitter' => $cs->twitter ?? null,
        'tiktok' => $cs->tiktok ?? null,
    ];
@endphp
@foreach($networks as $network => $url)
    @if(!empty($url))
                  <a
                    class="{{ $linkClass }}"
                    href="{{ e($url) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="{{ $network }} link"
                  >
                    @include('partials.homepage-social-icon', ['network' => $network])
                  </a>
    @endif
@endforeach
