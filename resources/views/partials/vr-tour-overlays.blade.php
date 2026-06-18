<div class="project-vr-logo-overlay" aria-label="Brand logo">
    <img src="{{ asset('theme/images/logo.png') }}" alt="{{ config('app.name') }}">
</div>
@if(!empty($overlayPhone))
<div class="project-vr-phone-overlay" aria-label="Contact phone">
    <i class="fa-solid fa-phone"></i>
    <span>{{ $overlayPhone }}</span>
</div>
@endif
<div class="project-vr-brand-overlay" aria-label="Brand">
    <i class="fa-regular fa-copyright"></i>
    <span>Etihad Marketing</span>
</div>
