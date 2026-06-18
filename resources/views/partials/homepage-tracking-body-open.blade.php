@php
    $siteSeo = $siteSeo ?? null;
@endphp
@if($siteSeo && !empty($siteSeo->google_tag_manager_id))
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ e($siteSeo->google_tag_manager_id) }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif
@if($siteSeo && !empty($siteSeo->facebook_pixel_id))
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ e($siteSeo->facebook_pixel_id) }}&ev=PageView&noscript=1" alt="" /></noscript>
@endif
@if($siteSeo && !empty($siteSeo->custom_body_open_code))
{!! $siteSeo->custom_body_open_code !!}
@endif
