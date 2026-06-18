@php
    $metaTitle = is_string($project->vr_tour_meta_title) && trim($project->vr_tour_meta_title) !== ''
        ? trim($project->vr_tour_meta_title)
        : (is_string($project->meta_title) && trim($project->meta_title) !== ''
            ? trim($project->meta_title) . ' - VR Tour'
            : ($project->title . ' - VR Tour - ' . config('app.name')));
    $metaDescriptionRaw = is_string($project->vr_tour_meta_description) && trim($project->vr_tour_meta_description) !== ''
        ? $project->vr_tour_meta_description
        : (is_string($project->meta_description) && trim($project->meta_description) !== ''
            ? $project->meta_description
            : (is_string($project->description) ? $project->description : ''));
    $metaDescription = \Illuminate\Support\Str::limit(strip_tags((string) $metaDescriptionRaw), 160);
    $metaKeywords = is_string($project->vr_tour_meta_keywords) && trim($project->vr_tour_meta_keywords) !== ''
        ? trim($project->vr_tour_meta_keywords)
        : (is_string($project->meta_keywords) ? trim($project->meta_keywords) : '');
    $canonical = is_string($project->vr_tour_canonical_url) && trim($project->vr_tour_canonical_url) !== ''
        ? trim($project->vr_tour_canonical_url)
        : (is_string($project->canonical_url) && trim($project->canonical_url) !== ''
            ? trim($project->canonical_url)
            : request()->url());
    $ogImage = $project->featured_image
        ? url('storage/' . ltrim($project->featured_image, '/'))
        : asset('theme/images/all/1.jpg');
@endphp
@include('layouts.vr-tour', [
    'metaTitle' => $metaTitle,
    'metaDescription' => $metaDescription,
    'metaKeywords' => $metaKeywords,
    'canonical' => $canonical,
    'ogImage' => $ogImage,
    'vrTourUrl' => $vrTourUrl,
    'overlayPhone' => $overlayPhone ?? '',
])
