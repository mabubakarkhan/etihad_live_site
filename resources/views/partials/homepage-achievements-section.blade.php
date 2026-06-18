@php
    $achievements = $achievements ?? \App\Models\HomepageAchievementsSetting::instance();
    $stats = $stats ?? \App\Models\HomepageAchievementsSetting::orderedStats();
@endphp
        <section class="stats-section">
          <div class="animated-bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
          </div>
          <div class="stats-container">
            <div class="stats-header">
              <h2 class="stats-title">{{ e($achievements->title_line_1) }} <span>{{ e($achievements->title_highlight) }}</span></h2>
            </div>
            <div class="stats-grid">
@foreach($stats as $index => $stat)
              <div class="stat-card" style="--delay: {{ $index * 0.1 }}s;">
                <div class="stat-number">{{ e($stat->value) }}@if(!empty($stat->suffix))<span style="font-size: 0.5em;">{{ e($stat->suffix) }}</span>@endif</div>
                <div class="stat-label">{{ e($stat->label) }}</div>
              </div>
@endforeach
            </div>
          </div>
        </section>
