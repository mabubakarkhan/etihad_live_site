@php
    $entity = $project ?? $phase ?? null;
    $entityType = isset($project) ? 'project' : 'phase';
    $seo = $entity
        ? seo_map_viewer_bundle($entity, request()->url(), $entityType)
        : [
            'title' => 'Interactive Map | ' . config('app.name'),
            'description' => '',
            'keywords' => '',
            'canonical' => url('/'),
            'type' => 'website',
            'robots' => 'noindex, nofollow, noarchive, nosnippet, noimageindex',
        ];
    $payload = base64_encode((string) ($embedUrl ?? ''));
@endphp
@include('layouts.map-embed', [
    'seo' => $seo,
    'payload' => $payload,
])
