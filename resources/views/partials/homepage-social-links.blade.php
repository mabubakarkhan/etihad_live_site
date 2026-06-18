@php
    $cs = $cs ?? \App\Models\ContactSetting::instance();
    $linkClass = $linkClass ?? 'menu-socials__link';
    $socials = [
        'facebook' => ['url' => $cs->facebook ?? '', 'label' => 'facebook link'],
        'instagram' => ['url' => $cs->instagram ?? '', 'label' => 'instagram link'],
        'linkedin' => ['url' => $cs->linkedin ?? '', 'label' => 'LinkedIn link'],
        'youtube' => ['url' => $cs->youtube ?? '', 'label' => 'YouTube link'],
        'twitter' => ['url' => $cs->twitter ?? '', 'label' => 'Twitter link'],
        'tiktok' => ['url' => $cs->tiktok ?? '', 'label' => 'TikTok link'],
    ];
@endphp
@foreach($socials as $network => $social)
    @if(!empty($social['url']))
                  <a
                    class="{{ $linkClass }}"
                    href="{{ e($social['url']) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="{{ $social['label'] }}"
                  >
                    @include('partials.homepage-social-icon', ['network' => $network])
                  </a>
    @endif
@endforeach
