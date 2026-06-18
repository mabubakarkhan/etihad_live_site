@php
    $journey = $journey ?? \App\Models\HomepageInvestmentJourneySetting::instance();
    $steps = $steps ?? \App\Models\HomepageInvestmentJourneySetting::orderedSteps();
@endphp
        <section class="timeline-section">
          <div class="animated-bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
          </div>
          <div class="timeline-container">
            <div class="timeline-header">
              <h2 class="timeline-title">{{ e($journey->title_line_1) }} <span>{{ e($journey->title_highlight) }}</span></h2>
            </div>
            <div class="timeline-content">
@foreach($steps as $step)
              <div class="timeline-item">
                <div class="timeline-item-content">
                  <div class="timeline-item-title">{{ e($step->title) }}</div>
                  <div class="timeline-item-description">{{ e($step->description) }}</div>
                </div>
              </div>
@endforeach
            </div>
          </div>
        </section>
