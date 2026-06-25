@php
    $tabsFollowHtml = trim((string) ($project->tabs_follow_content ?? ''));
    $tabsFollowPlain = trim(strip_tags($tabsFollowHtml));
@endphp
@if($tabsFollowPlain !== '')
<section class="project-tabs-follow-content" id="project-tabs-follow-content">
    <div class="project-tabs-follow-content__inner">
        <div class="project-tabs-follow-content__body is-collapsed" data-tabs-follow-body>
            <div class="project-tabs-follow-content__richtext">
                {!! $tabsFollowHtml !!}
            </div>
        </div>
        <div class="project-tabs-follow-content__toggle-wrap" data-tabs-follow-toggle-wrap hidden>
            <button type="button" class="project-tabs-follow-content__toggle" data-tabs-follow-toggle aria-expanded="false">
                Read more
            </button>
        </div>
    </div>
</section>
@endif
