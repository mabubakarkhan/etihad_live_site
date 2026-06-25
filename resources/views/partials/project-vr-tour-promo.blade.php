@if($project->hasVrTourPromo() && !empty($vrTourPageUrl))
<section class="project-vr-tour-promo" id="project-vr-tour-promo">
    <div class="container">
        <div class="project-vr-tour-promo__inner">
            <h3 class="project-vr-tour-promo__title">Virtual tour of {{ $project->title }}</h3>
            <a href="{{ $vrTourPageUrl }}" class="project-vr-tour-promo__link" target="_blank" rel="noopener">
                <span class="project-vr-tour-promo__image-wrap">
                    <img
                        src="{{ $project->vrTourImageUrl() }}"
                        alt="Virtual tour of {{ $project->title }}"
                        class="project-vr-tour-promo__image"
                        loading="lazy"
                    >
                    <span class="project-vr-tour-promo__overlay" aria-hidden="true">
                        <span class="project-vr-tour-promo__play"><i class="fa-solid fa-vr-cardboard"></i></span>
                        <span class="project-vr-tour-promo__cta-text">Open virtual tour</span>
                    </span>
                </span>
            </a>
        </div>
    </div>
</section>
@endif
