@php
    $phase = $phase ?? null;
    if (! $phase) {
        return;
    }
    $cta = $phase->finalCta();
    if ($cta === null) {
        return;
    }

    $inquiryPropertyTypes = \App\Models\ProjectType::query()->orderBy('name')->pluck('name')
        ->map(fn ($n) => trim((string) $n))->filter()->unique()->values()->all();
    if ($inquiryPropertyTypes === []) {
        $inquiryPropertyTypes = ['House', 'Plot', 'Apartment', 'Commercial'];
    }
    $inquiryBudgetOptions = [
        'Up to PKR 50 Lakh',
        'PKR 50 Lakh – 1 Crore',
        'PKR 1 – 2 Crore',
        'PKR 2 – 5 Crore',
        'PKR 5 Crore+',
    ];
@endphp

<section class="dha-final-cta" id="dha-final-cta" aria-labelledby="dha-final-cta-title">
    <div class="dha-final-cta__inner">
        <div class="dha-final-cta__glow" aria-hidden="true"></div>
        <div class="dha-final-cta__layout">
            <div class="dha-final-cta__copy">
                <span class="dha-final-cta__eyebrow">Expert Guidance</span>
                <h2 class="dha-final-cta__title" id="dha-final-cta-title">{{ $cta['heading'] }}</h2>
                <p class="dha-final-cta__lead">Share a few details and our advisors will help you move faster with verified options for {{ $phase->title }}.</p>
                <ul class="dha-final-cta__benefits">
                    @foreach($cta['benefits'] as $benefit)
                        <li>
                            <span class="dha-final-cta__check" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span>{{ $benefit }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="dha-final-cta__form-card">
                <form
                    method="post"
                    action="{{ route('dha.phase.request-info', $phase) }}"
                    class="dha-final-cta__form"
                    id="dha-final-cta-form"
                >
                    @csrf
                    <div class="dha-final-cta__form-row">
                        <input type="text" name="name" placeholder="Name" required autocomplete="name">
                        <input type="tel" name="phone" placeholder="Phone" required autocomplete="tel">
                    </div>
                    <div class="dha-final-cta__form-row">
                        <select name="property_type" aria-label="Property type">
                            <option value="">Property Type</option>
                            @foreach($inquiryPropertyTypes as $ptype)
                                <option value="{{ $ptype }}">{{ $ptype }}</option>
                            @endforeach
                        </select>
                        <select name="budget" aria-label="Budget">
                            <option value="">Budget</option>
                            @foreach($inquiryBudgetOptions as $budgetOpt)
                                <option value="{{ $budgetOpt }}">{{ $budgetOpt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="dha-final-cta__form-msg" id="dha-final-cta-msg" aria-live="polite"></div>
                    <button type="submit" class="dha-final-cta__submit" id="dha-final-cta-submit">
                        {{ $cta['cta_label'] }}
                        <i data-lucide="arrow-right" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    var form = document.getElementById('dha-final-cta-form');
    var submitBtn = document.getElementById('dha-final-cta-submit');
    var message = document.getElementById('dha-final-cta-msg');
    if (!form || !submitBtn || !message) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        submitBtn.disabled = true;
        message.className = 'dha-final-cta__form-msg';
        message.textContent = '';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(function (res) {
            return res.json().catch(function () { return { success: false, message: 'Invalid response.' }; });
        }).then(function (json) {
            if (json && json.success) {
                message.className = 'dha-final-cta__form-msg is-success';
                message.textContent = json.message || 'Your request has been sent successfully.';
                form.reset();
            } else {
                message.className = 'dha-final-cta__form-msg is-error';
                message.textContent = (json && json.message) || 'Something went wrong. Please try again.';
            }
        }).catch(function () {
            message.className = 'dha-final-cta__form-msg is-error';
            message.textContent = 'Something went wrong. Please try again.';
        }).finally(function () {
            submitBtn.disabled = false;
        });
    });
})();
</script>
