@php
    $bookingProcedure = $project->bookingProcedureData();
    $bpHeading = $bookingProcedure['heading'] ?? '';
    $bpContent = $bookingProcedure['content'] ?? '';
    $bpDocsHeading = $bookingProcedure['documents_heading'] ?? 'Required Documents';
    $bpSteps = $bookingProcedure['steps'] ?? [];
    $bpDocuments = $bookingProcedure['documents'] ?? [];
@endphp
@if($project->hasBookingProcedure())
<section class="project-booking-procedure" id="project-booking-procedure">
    <div class="project-booking-procedure__inner">
            @if($bpHeading !== '')
                <h2 class="project-booking-procedure__title">{{ $bpHeading }}</h2>
            @endif

            @if(trim(strip_tags($bpContent)) !== '')
                <div class="project-booking-procedure__intro">{!! $bpContent !!}</div>
            @endif

            @if(count($bpSteps) > 0)
                <div class="project-booking-procedure__steps">
                    @foreach($bpSteps as $stepIndex => $step)
                        @php
                            $isAccent = $stepIndex % 2 === 1;
                            $stepNumber = str_pad((string) ($stepIndex + 1), 2, '0', STR_PAD_LEFT);
                        @endphp
                        <article class="project-booking-procedure__step {{ $isAccent ? 'is-accent' : 'is-light' }}">
                            <div class="project-booking-procedure__step-num">{{ $stepNumber }}</div>
                            <div class="project-booking-procedure__step-divider" aria-hidden="true"></div>
                            @if(trim((string) ($step['title'] ?? '')) !== '')
                                <h3 class="project-booking-procedure__step-title">{{ $step['title'] }}</h3>
                            @endif
                            @if(trim((string) ($step['description'] ?? '')) !== '')
                                <p class="project-booking-procedure__step-desc">{{ $step['description'] }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif

            @if(count($bpDocuments) > 0)
                <div class="project-booking-procedure__documents">
                    <h3 class="project-booking-procedure__documents-title">{{ $bpDocsHeading }}</h3>
                    <div class="project-booking-procedure__documents-grid">
                        @foreach($bpDocuments as $document)
                            @php
                                $docIcon = trim((string) ($document['icon'] ?? 'fa-circle-check'));
                                if ($docIcon !== '' && ! str_contains($docIcon, 'fa-')) {
                                    $docIcon = 'fa-' . ltrim($docIcon, '-');
                                }
                            @endphp
                            <div class="project-booking-procedure__document">
                                <span class="project-booking-procedure__document-icon" aria-hidden="true">
                                    <i class="fa-light {{ $docIcon }}"></i>
                                </span>
                                <p class="project-booking-procedure__document-label">{{ $document['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
    </div>
</section>
@endif
