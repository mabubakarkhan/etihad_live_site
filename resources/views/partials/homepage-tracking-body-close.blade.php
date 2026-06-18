@php
    $siteSeo = $siteSeo ?? null;
@endphp
@if($siteSeo && !empty($siteSeo->custom_body_close_code))
{!! $siteSeo->custom_body_close_code !!}
@endif
