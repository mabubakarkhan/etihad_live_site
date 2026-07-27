@php
    $phase = $phase ?? null;
    if (! $phase) {
        return;
    }

    $factors = $phase->investmentScorecardFactors();
    if ($factors === []) {
        return;
    }

    $overall = $phase->investmentScoreOverall();
    $overallLabel = number_format($overall, 1);
    $overallPct = max(0, min(100, ($overall / 10) * 100));
@endphp
<section class="dha-scorecard" id="dha-investment-scorecard" aria-labelledby="dha-scorecard-title">
    <div class="dha-scorecard__inner">
        <header class="dha-scorecard__head">
            <span class="dha-scorecard__eyebrow">Unique Feature</span>
            <h2 class="dha-scorecard__title" id="dha-scorecard-title">Investment Scorecard</h2>
            <p class="dha-scorecard__lead">Rate the phase across key investment factors for {{ $phase->title }}.</p>
        </header>

        <div class="dha-scorecard__layout">
            <div class="dha-scorecard__overall" aria-label="Overall investment score">
                <div class="dha-scorecard__ring" style="--score-pct: {{ $overallPct }}">
                    <div class="dha-scorecard__ring-core">
                        <span class="dha-scorecard__ring-value">{{ $overallLabel }}</span>
                        <span class="dha-scorecard__ring-max">/10</span>
                    </div>
                </div>
                <strong class="dha-scorecard__overall-label">Investment Score</strong>
                <span class="dha-scorecard__overall-note">Overall rating</span>
            </div>

            <div class="dha-scorecard__factors">
                <div class="dha-scorecard__table-head" aria-hidden="true">
                    <span>Factor</span>
                    <span>Score</span>
                </div>
                @foreach($factors as $factor)
                    @php
                        $score = (float) $factor['score'];
                        $pct = max(0, min(100, ($score / 10) * 100));
                    @endphp
                    <article class="dha-scorecard__factor">
                        <div class="dha-scorecard__factor-top">
                            <span class="dha-scorecard__factor-icon" aria-hidden="true">
                                <i data-lucide="{{ $factor['icon'] }}"></i>
                            </span>
                            <strong class="dha-scorecard__factor-label">{{ $factor['label'] }}</strong>
                            <span class="dha-scorecard__factor-score">{{ rtrim(rtrim(number_format($score, 1), '0'), '.') }}/10</span>
                        </div>
                        <div class="dha-scorecard__bar" role="presentation">
                            <span class="dha-scorecard__bar-fill" style="width: {{ $pct }}%"></span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
