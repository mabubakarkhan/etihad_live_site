@extends('layouts.front')
@php
    $jobTitle = $career->meta_title ?: ($career->title . ' | Careers – ' . config('app.name'));
    $cmsPage = $cmsPage ?? null;
    $bannerImage = ($cmsPage && $cmsPage->banner_image) ? url('storage/' . ltrim($cmsPage->banner_image, '/')) : asset('theme/images/bg/8.jpg');
    $subheading = trim(implode(' · ', array_filter([$career->department, $career->location, $career->employment_type])));
@endphp
@section('title', $jobTitle)
@if($career->meta_description)
@push('meta')
<meta name="description" content="{{ e($career->meta_description) }}">
@if(!empty($career->meta_keywords))<meta name="keywords" content="{{ e($career->meta_keywords) }}">@endif
@if(!empty($career->canonical_url))<link rel="canonical" href="{{ e($career->canonical_url) }}">@endif
@endpush
@endif

@push('styles')
<style>
.career-job-page .job-description-section { text-align: left; }
.career-job-page .job-description-section .boxed-content-title { text-align: left; }
.career-job-page .job-description { font-size: 15px; line-height: 1.85; color: #334155; text-align: left; }
.career-job-page .job-description p { margin-bottom: 12px; }
.career-job-page .job-meta-list { list-style: none; margin: 0; padding: 0; display: grid; gap: 10px; }
.career-job-page .job-meta-item { display: flex; gap: 10px; align-items: flex-start; color: #475569; font-size: 13px; }
.career-job-page .job-meta-item i { color: var(--theme-color, #e85d04); margin-top: 2px; width: 18px; text-align: center; }
.career-job-page .job-meta-item strong { color: #0f172a; font-weight: 700; margin-right: 6px; }
.career-job-page .job-status-wrap { text-align: left; margin-bottom: 12px; }
.career-job-page .job-status-pill { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 10px; font-size: 12px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
.career-job-page .job-status-pill.status-open { background: rgba(34, 197, 94, .15); border: 1px solid rgba(34, 197, 94, .35); color: #15803d; }
.career-job-page .job-status-pill.status-closed { background: rgba(100, 116, 139, .15); border: 1px solid rgba(100, 116, 139, .3); color: #475569; }
.career-job-page .job-status-pill.status-draft { background: rgba(245, 158, 11, .15); border: 1px solid rgba(245, 158, 11, .35); color: #b45309; }
.career-job-page .job-apply-box { text-align: left; }
.career-job-page .job-apply-msg { margin-top: 10px; padding: 10px; border-radius: 8px; display: none; }
.career-job-page .job-apply-msg.success { background: #dcfce7; color: #166534; display: block; }
.career-job-page .job-apply-msg.error { background: #fee2e2; color: #991b1b; display: block; }
.career-job-page .job-cv-email { margin-bottom: 12px; }
.career-job-page .job-cv-email a { color: var(--theme-color, #e85d04); font-weight: 600; }
.career-job-page .custom-form .cs-intputwrap input[type="file"] { width: 100%; padding: 10px; border: 1px solid #eee; background: #f9f9f9; border-radius: 4px; font-size: 14px; }
/* OR separator: bigger, centered, line stops at OR then continues */
.career-job-page .job-or-separator { display: flex; align-items: center; justify-content: center; gap: 0; margin: 18px 0; text-align: center; }
.career-job-page .job-or-separator::before,
.career-job-page .job-or-separator::after { content: ''; flex: 1; max-width: 120px; height: 1px; background: #ddd; }
.career-job-page .job-or-separator span { padding: 0 20px; font-size: 1.35rem; font-weight: 800; letter-spacing: 0.08em; color: #64748b; white-space: nowrap; }
</style>
@endpush

@push('scripts')
<script>
(function() {
    var form = document.getElementById('job-apply-form');
    var msg = document.getElementById('job-apply-msg');
    var btn = document.getElementById('job-apply-btn');
    if (!form || !msg) return;
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (btn) { btn.disabled = true; btn.textContent = 'Submitting...'; }
        msg.className = 'job-apply-msg';
        msg.textContent = '';
        var fd = new FormData(form);
        fetch(form.action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(function(r) { return r.json().then(function(j) { return { ok: r.ok, json: j }; }); })
            .then(function(res) {
                if (btn) { btn.disabled = false; btn.textContent = 'Submit application'; }
                if (res.ok && res.json.success) {
                    msg.className = 'job-apply-msg success';
                    msg.textContent = res.json.message || 'Application submitted successfully. If you provided an email, we have sent you a confirmation.';
                    form.reset();
                } else {
                    msg.className = 'job-apply-msg error';
                    msg.textContent = (res.json && res.json.message) || (res.json && res.json.errors && Object.values(res.json.errors).flat()[0]) || 'Something went wrong. Please try again.';
                }
            })
            .catch(function() {
                if (btn) { btn.disabled = false; btn.textContent = 'Submit application'; }
                msg.className = 'job-apply-msg error';
                msg.textContent = 'Network error. Please try again.';
            });
    });
})();
</script>
@endpush

@section('content')
<div id="main">
    @include('partials.header')
    <div class="wrapper">
        <div class="content">
            <div class="section hero-section hero-section_sin">
                <div class="hero-section-wrap">
                    <div class="hero-section-wrap-item">
                        <div class="container">
                            <div class="hero-section-container">
                                <div class="hero-section-title">
                                    <h2>{{ $career->title }}</h2>
                                    @if($subheading)<h5>{{ $subheading }}</h5>@endif
                                </div>
                            </div>
                        </div>
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper"><div class="bg" data-bg="{{ $bannerImage }}"></div></div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ url('/careers') }}">Careers</a>
                    <span>{{ $career->title }}</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>
                <div class="main-content career-job-page">
                    <div class="boxed-container">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="boxed-content job-description-section">
                                    <div class="boxed-content-title">
                                        <h3>Job Description</h3>
                                    </div>
                                    <div class="boxed-content-item">
                                        @if($career->requirements)
                                        <div class="property-description job-description">{!! $career->requirements !!}</div>
                                        @else
                                        <p class="text-muted">No description available for this job.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="sb-container">
                                    @php
                                        $status = $career->status ?? 'active';
                                        $statusLabel = $status === 'active' ? 'Open' : ($status === 'closed' ? 'Closed' : 'Draft');
                                        $statusClass = $status === 'active' ? 'status-open' : ($status === 'closed' ? 'status-closed' : 'status-draft');
                                        $applyBy = $career->apply_before ? \Carbon\Carbon::parse($career->apply_before)->format('d M Y') : null;
                                        $applyEmail = $career->apply_email ?: \App\Models\ContactSetting::instance()->email;
                                    @endphp
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>Job Details</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="job-status-wrap"><span class="job-status-pill {{ $statusClass }}"><i class="fa-light fa-circle-dot"></i> {{ $statusLabel }}</span></div>
                                            <ul class="job-meta-list">
                                                @if($career->department)<li class="job-meta-item"><i class="fa-light fa-briefcase"></i><div><strong>Department</strong> {{ $career->department }}</div></li>@endif
                                                @if($career->location)<li class="job-meta-item"><i class="fa-light fa-location-dot"></i><div><strong>Location</strong> {{ $career->location }}</div></li>@endif
                                                @if($career->employment_type)<li class="job-meta-item"><i class="fa-light fa-id-badge"></i><div><strong>Type</strong> {{ $career->employment_type }}</div></li>@endif
                                                @if($career->education)<li class="job-meta-item"><i class="fa-light fa-graduation-cap"></i><div><strong>Education</strong> {{ $career->education }}</div></li>@endif
                                                @if($career->experience)<li class="job-meta-item"><i class="fa-light fa-briefcase-clock"></i><div><strong>Experience</strong> {{ $career->experience }}</div></li>@endif
                                                @if($career->timings)<li class="job-meta-item"><i class="fa-light fa-clock"></i><div><strong>Timings</strong> {{ $career->timings }}</div></li>@endif
                                                @if($career->joining_month)<li class="job-meta-item"><i class="fa-light fa-calendar"></i><div><strong>Joining</strong> {{ $career->joining_month }}</div></li>@endif
                                                @if($career->vacancies !== null)<li class="job-meta-item"><i class="fa-light fa-users"></i><div><strong>Vacancies</strong> {{ $career->vacancies }}</div></li>@endif
                                                @if($career->salary_range)<li class="job-meta-item"><i class="fa-light fa-money-bill-wave"></i><div><strong>Salary</strong> {{ $career->salary_range }}</div></li>@endif
                                                @if($applyBy)<li class="job-meta-item"><i class="fa-light fa-calendar-days"></i><div><strong>Apply by</strong> {{ $applyBy }}</div></li>@endif
                                            </ul>
                                        </div>
                                    </div>

                                    @if($applyEmail || $career->apply_url || $career->status === 'active')
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>Apply Now</h3>
                                        </div>
                                        <div class="boxed-content-item job-apply-box">
                                            @if($applyEmail)
                                            <p class="job-cv-email">Send your CV on email <a href="mailto:{{ e($applyEmail) }}">{{ $applyEmail }}</a></p>
                                            @endif
                                            @if($career->status === 'active')
                                            <div class="job-or-separator"><span>OR</span></div>
                                            <p style="margin-bottom: 14px;">Use the form below to submit your application.</p>
                                            <div class="custom-form property-request-form no-icons" id="job_apply_cf">
                                                <form id="job-apply-form" action="{{ route('careers.apply', $career->slug) }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="cs-intputwrap">
                                                        <input name="name" type="text" placeholder="Your name *" required maxlength="255">
                                                    </div>
                                                    <div class="cs-intputwrap">
                                                        <input name="mobile" type="text" placeholder="Mobile *" required maxlength="50">
                                                    </div>
                                                    <div class="cs-intputwrap">
                                                        <input name="email" type="email" placeholder="Email (for confirmation)" maxlength="255">
                                                    </div>
                                                    <div class="cs-intputwrap">
                                                        <input name="address" type="text" placeholder="Address" maxlength="500">
                                                    </div>
                                                    <div class="cs-intputwrap">
                                                        <input name="city" type="text" placeholder="City" maxlength="120">
                                                    </div>
                                                    <div class="cs-intputwrap">
                                                        <input name="education" type="text" placeholder="Education" maxlength="255">
                                                    </div>
                                                    <div class="cs-intputwrap">
                                                        <input name="cv" type="file" accept=".pdf,.doc,.docx" id="ja_cv">
                                                    </div>
                                                    <div class="cs-intputwrap">
                                                        <textarea name="comments" placeholder="Comments (optional)" rows="3" maxlength="5000"></textarea>
                                                    </div>
                                                    <div class="job-apply-msg" id="job-apply-msg"></div>
                                                    <button type="submit" class="commentssubmit commentssubmit_fw" id="job-apply-btn">Submit application</button>
                                                </form>
                                            </div>
                                            @endif
                                            @if($career->apply_url && $career->status !== 'active')
                                            <p style="margin-top: 12px;"><a href="{{ e($career->apply_url) }}" class="commentssubmit commentssubmit_fw"><i class="fa-light fa-arrow-up-right-from-square"></i> Apply online</a></p>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="to_top-btn-wrap">
                    <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                    <div class="svg-corner svg-corner_white" style="top:0;left: -40px; transform: rotate(-90deg)"></div>
                    <div class="svg-corner svg-corner_white" style="top:0;right: -40px; transform: rotate(-180deg)"></div>
                </div>
            </div>
        </div>
        @include('partials.footer')
    </div>
    @include('partials.theme-panels')
</div>
@endsection
