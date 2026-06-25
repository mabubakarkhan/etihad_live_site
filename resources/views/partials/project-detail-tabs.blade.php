@php
    $detailTabs = is_array($project->project_detail_tabs ?? null) ? $project->project_detail_tabs : [];
    $detailTabs = array_values(array_filter($detailTabs, function ($tab) {
        if (! is_array($tab)) {
            return false;
        }
        $label = trim((string) ($tab['label'] ?? ''));
        $heading = trim((string) ($tab['heading'] ?? ''));
        $detail = trim(strip_tags((string) ($tab['detail'] ?? '')));
        $bullets = trim((string) ($tab['bullets'] ?? ''));
        $images = is_array($tab['images'] ?? null) ? array_filter($tab['images']) : [];

        return $label !== '' || $heading !== '' || $detail !== '' || $bullets !== '' || $images !== [];
    }));
    $detailTabsPhone = preg_replace('/\s+/', '', (string) ($projectPhoneClean ?? ''));
@endphp
@if(count($detailTabs) > 0)
<section class="project-detail-tabs" id="project-detail-tabs">
    <div class="container">
        <div class="project-detail-tabs__frame">
            <div class="project-detail-tabs__nav" role="tablist" aria-label="Project details">
                @foreach($detailTabs as $tabIndex => $tab)
                    @php
                        $tabLabel = trim((string) ($tab['label'] ?? ('Tab ' . ($tabIndex + 1))));
                        $tabIcon = trim((string) ($tab['icon'] ?? 'fa-circle-info'));
                        if ($tabIcon !== '' && ! str_contains($tabIcon, 'fa-')) {
                            $tabIcon = 'fa-' . ltrim($tabIcon, '-');
                        }
                    @endphp
                    <button
                        type="button"
                        class="project-detail-tabs__nav-btn {{ $tabIndex === 0 ? 'is-active' : '' }}"
                        role="tab"
                        id="project-detail-tab-btn-{{ $tabIndex }}"
                        aria-selected="{{ $tabIndex === 0 ? 'true' : 'false' }}"
                        aria-controls="project-detail-tab-panel-{{ $tabIndex }}"
                        data-tab-index="{{ $tabIndex }}"
                    >
                        <i class="fa-light {{ $tabIcon }}"></i>
                        <span>{{ $tabLabel }}</span>
                    </button>
                @endforeach
            </div>

            <div class="project-detail-tabs__body">
                @foreach($detailTabs as $tabIndex => $tab)
            @php
                $tabHeading = trim((string) ($tab['heading'] ?? ''));
                $tabDetail = (string) ($tab['detail'] ?? '');
                $tabBulletsRaw = trim((string) ($tab['bullets'] ?? ''));
                $tabBullets = $tabBulletsRaw !== ''
                    ? array_values(array_filter(array_map('trim', preg_split('/\s*,\s*/', $tabBulletsRaw))))
                    : [];
                $tabImages = is_array($tab['images'] ?? null) ? array_values(array_filter($tab['images'])) : [];
                $tabImageUrls = array_values(array_filter(array_map(function ($path) {
                    $path = trim((string) $path);
                    if ($path === '') {
                        return null;
                    }

                    return asset('storage/' . ltrim($path, '/'));
                }, $tabImages)));
            @endphp
            <div
                class="project-detail-tabs__panel {{ $tabIndex === 0 ? 'is-active' : '' }}"
                role="tabpanel"
                id="project-detail-tab-panel-{{ $tabIndex }}"
                aria-labelledby="project-detail-tab-btn-{{ $tabIndex }}"
                @if($tabIndex !== 0) hidden @endif
            >
                <div class="project-detail-tabs__panel-inner">
                    @if($tabHeading !== '')
                        <h3 class="project-detail-tabs__heading">{{ $tabHeading }}</h3>
                    @endif

                    @if(trim(strip_tags($tabDetail)) !== '')
                        <div class="project-detail-tabs__detail">{!! $tabDetail !!}</div>
                    @endif

                    @if(count($tabBullets) > 0)
                        <ul class="project-detail-tabs__bullets">
                            @foreach($tabBullets as $bullet)
                                <li>{{ $bullet }}</li>
                            @endforeach
                        </ul>
                    @endif

                    @if(count($tabImageUrls) > 1)
                        <div class="project-detail-tabs__media">
                            <div class="project-detail-tabs__slider" data-slider-id="detail-tab-slider-{{ $tabIndex }}">
                                @foreach($tabImageUrls as $imgUrl)
                                    <div class="project-detail-tabs__slide">
                                        <img src="{{ $imgUrl }}" alt="{{ $tabHeading !== '' ? $tabHeading : $project->title }}" loading="lazy">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif(count($tabImageUrls) === 1)
                        <div class="project-detail-tabs__media">
                            <div class="project-detail-tabs__single-image">
                                <img src="{{ $tabImageUrls[0] }}" alt="{{ $tabHeading !== '' ? $tabHeading : $project->title }}" loading="lazy">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
                @endforeach
            </div>

            @if($detailTabsPhone !== '')
                <div class="project-detail-tabs__cta-wrap">
                    <a href="tel:{{ $detailTabsPhone }}" class="project-detail-tabs__cta-btn">Contact Us Now</a>
                </div>
            @endif
        </div>
    </div>
</section>
@endif
