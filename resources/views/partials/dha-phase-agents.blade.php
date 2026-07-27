@php
    $phase = $phase ?? null;
    if (! $phase) {
        return;
    }

    $agents = $phase->featuredAgentsForDisplay(2);
@endphp
@if($agents->isNotEmpty())
<section class="dha-agents" id="dha-meet-agents" aria-labelledby="dha-agents-title">
    <div class="dha-agents__inner">
        <header class="dha-agents__head">
            <h2 class="dha-agents__title" id="dha-agents-title">Meet The Agents</h2>
            <p class="dha-agents__lead">Connect with trusted agents helping buyers and sellers across {{ $phase->title }}.</p>
        </header>

        <div class="dha-agents__grid">
            @foreach($agents as $agent)
                @php
                    $imageUrl = ! empty($agent->profile_pic)
                        ? url('storage/' . ltrim($agent->profile_pic, '/'))
                        : asset('theme/images/all/1.jpg');
                    $propsCount = (int) ($agent->phase_properties_count ?? $agent->properties_count ?? 0);
                    $location = trim(implode(', ', array_filter([
                        trim((string) ($agent->city ?? '')),
                        trim((string) ($agent->state ?? '')),
                    ]))) ?: 'Lahore';
                    $profileUrl = $agent->slug ? route('dealer.show', $agent->slug) : null;
                    $phone = trim((string) ($agent->phone ?: $agent->mobile ?: ''));
                    $telHref = $phone !== '' ? 'tel:' . preg_replace('/\s+/', '', $phone) : '';
                @endphp
                <article class="dha-agents__card">
                    <div class="dha-agents__media">
                        <img src="{{ $imageUrl }}" alt="{{ $agent->name }}" loading="lazy" decoding="async">
                    </div>
                    <div class="dha-agents__body">
                        <h3 class="dha-agents__name">
                            @if($profileUrl)
                                <a href="{{ $profileUrl }}">{{ $agent->name }}</a>
                            @else
                                {{ $agent->name }}
                            @endif
                        </h3>
                        <p class="dha-agents__meta">{{ $location }}</p>
                        <p class="dha-agents__stats">{{ $propsCount === 1 ? '1 Property' : number_format($propsCount) . ' Properties' }}</p>
                        <div class="dha-agents__actions">
                            @if($profileUrl)
                                <a href="{{ $profileUrl }}" class="dha-agents__btn dha-agents__btn--primary">View Profile</a>
                            @endif
                            @if($telHref !== '')
                                <a href="{{ $telHref }}" class="dha-agents__btn dha-agents__btn--ghost">
                                    <i data-lucide="phone" aria-hidden="true"></i>
                                    Call
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
