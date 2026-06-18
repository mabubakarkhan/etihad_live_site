@php
    $whyItems = old('why_choose_items', $dha->whyChooseItems());
    $storedLc = is_array($dha->lifestyle_cards) ? $dha->lifestyle_cards : [];
    $lifestyleCards = old('lifestyle_cards');
    if ($lifestyleCards === null) {
        $lifestyleCards = [];
        foreach (\App\Models\DhaSetting::defaultLifestyleCards() as $i => $def) {
            $row = is_array($storedLc[$i] ?? null) ? $storedLc[$i] : [];
            $lifestyleCards[] = [
                'label' => $row['label'] ?? $def['label'],
                'image' => $row['image'] ?? '',
            ];
        }
    }
    $growthStats = old('growth_stats', $dha->growthStats());
    $phasesHead = $dha->phasesHeading();
    $ctaBannerPath = old('cta_banner_image_path', '');
    $existingCtaBanner = (!$ctaBannerPath && $dha->cta_banner_image) ? $dha->cta_banner_image : '';
@endphp

<div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
    <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Phases section heading</h2>
    <div class="grid md:grid-cols-3 gap-4">
        <div><label class="block text-sm mb-1">Eyebrow</label>
            <input name="phases_heading_eyebrow" type="text" value="{{ old('phases_heading_eyebrow', $dha->phases_heading_eyebrow ?? $phasesHead['eyebrow']) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
        <div><label class="block text-sm mb-1">Title (gold)</label>
            <input name="phases_heading_gold" type="text" value="{{ old('phases_heading_gold', $dha->phases_heading_gold ?? $phasesHead['gold']) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
        <div><label class="block text-sm mb-1">Title (white)</label>
            <input name="phases_heading_white" type="text" value="{{ old('phases_heading_white', $dha->phases_heading_white ?? $phasesHead['white']) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
    </div>
    <div class="grid md:grid-cols-2 gap-4">
        <div><label class="block text-sm mb-1">After phases button label</label>
            <input name="view_all_label" type="text" value="{{ old('view_all_label', $dha->view_all_label) }}" placeholder="VIEW ALL PROPERTIES" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
        <div><label class="block text-sm mb-1">Button URL</label>
            <input name="view_all_url" type="text" value="{{ old('view_all_url', $dha->view_all_url) }}" placeholder="/listing" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
    </div>
</div>

<div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
    <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Why choose DHA</h2>
    <div><label class="block text-sm mb-1">Heading</label>
        <input name="why_choose_heading" type="text" value="{{ old('why_choose_heading', $dha->why_choose_heading) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
    @foreach(range(0, 5) as $i)
        @php $w = $whyItems[$i] ?? ['icon' => '', 'title' => '', 'text' => '']; @endphp
        <div class="grid md:grid-cols-3 gap-2 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800">
            <input name="why_choose_items[{{ $i }}][icon]" type="text" value="{{ $w['icon'] ?? '' }}" placeholder="Lucide icon" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
            <input name="why_choose_items[{{ $i }}][title]" type="text" value="{{ $w['title'] ?? '' }}" placeholder="Title" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
            <input name="why_choose_items[{{ $i }}][text]" type="text" value="{{ $w['text'] ?? '' }}" placeholder="Description" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
        </div>
    @endforeach
</div>

<div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
    <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Lifestyle section</h2>
    <div class="grid md:grid-cols-2 gap-4">
        <div><label class="block text-sm mb-1">Eyebrow</label><input name="lifestyle_eyebrow" type="text" value="{{ old('lifestyle_eyebrow', $dha->lifestyle_eyebrow) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
        <div><label class="block text-sm mb-1">Heading</label><input name="lifestyle_heading" type="text" value="{{ old('lifestyle_heading', $dha->lifestyle_heading) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
    </div>
    <div><label class="block text-sm mb-1">Description</label><textarea name="lifestyle_description" rows="3" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm">{{ old('lifestyle_description', $dha->lifestyle_description) }}</textarea></div>
    <div class="grid md:grid-cols-2 gap-4">
        <div><label class="block text-sm mb-1">Button label</label><input name="lifestyle_btn_label" type="text" value="{{ old('lifestyle_btn_label', $dha->lifestyle_btn_label) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
        <div><label class="block text-sm mb-1">Button URL</label><input name="lifestyle_btn_url" type="text" value="{{ old('lifestyle_btn_url', $dha->lifestyle_btn_url) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
    </div>
    <h3 class="text-xs font-semibold uppercase text-slate-500">Lifestyle cards (6)</h3>
    @foreach(range(0, 5) as $i)
        @php $lc = $lifestyleCards[$i] ?? ['label' => '', 'image' => '']; @endphp
        <div class="grid md:grid-cols-2 gap-2 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800">
            <input name="lifestyle_cards[{{ $i }}][label]" type="text" value="{{ $lc['label'] ?? '' }}" placeholder="Label" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
            <input name="lifestyle_cards[{{ $i }}][image]" type="text" value="{{ $lc['image'] ?? '' }}" placeholder="Image path (storage)" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
        </div>
    @endforeach
</div>

<div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
    <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Growth stats bar</h2>
    <div><label class="block text-sm mb-1">Heading</label><input name="growth_heading" type="text" value="{{ old('growth_heading', $dha->growth_heading) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
    @foreach(range(0, 4) as $i)
        @php $gs = $growthStats[$i] ?? ['icon' => '', 'value' => '', 'label' => '']; @endphp
        <div class="grid md:grid-cols-3 gap-2 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800">
            <input name="growth_stats[{{ $i }}][icon]" type="text" value="{{ $gs['icon'] ?? '' }}" placeholder="Icon" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
            <input name="growth_stats[{{ $i }}][value]" type="text" value="{{ $gs['value'] ?? '' }}" placeholder="Value" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
            <input name="growth_stats[{{ $i }}][label]" type="text" value="{{ $gs['label'] ?? '' }}" placeholder="Label" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
        </div>
    @endforeach
</div>

<div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4" data-media-wrap>
    <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Bottom CTA banner</h2>
    <input type="hidden" name="cta_banner_image_path" id="dha-cta-banner-path" value="{{ $ctaBannerPath }}" />
    <div id="dha-cta-banner-existing" class="{{ $existingCtaBanner && !$ctaBannerPath ? '' : 'hidden' }} mb-2 flex items-center gap-3 flex-wrap">
        @if($existingCtaBanner)<img src="{{ asset('storage/' . $existingCtaBanner) }}" alt="" class="h-20 rounded border object-cover" />@endif
        <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 cursor-pointer">
            <input type="checkbox" name="remove_cta_banner_image" value="1" class="rounded" {{ old('remove_cta_banner_image') ? 'checked' : '' }} /> Remove banner image
        </label>
    </div>
    <div id="dha-cta-banner-preview" class="{{ $ctaBannerPath ? '' : 'hidden' }} mb-2"></div>
    <div id="dha-cta-banner-upload-status" class="hidden mb-2 text-sm text-sky-700">Uploading…</div>
    <div id="dha-cta-banner-upload-error" class="hidden mb-2 text-sm text-rose-700"></div>
    <input type="file" accept="image/*" class="dha-media-upload block w-full text-sm" data-upload-type="cta_banner" data-path-name="cta_banner_image_path" data-status-prefix="dha-cta-banner" />
    <div class="grid md:grid-cols-2 gap-4">
        <div><label class="block text-sm mb-1">Title (gold)</label><input name="cta_title_gold" type="text" value="{{ old('cta_title_gold', $dha->cta_title_gold) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
        <div><label class="block text-sm mb-1">Title (white)</label><input name="cta_title_white" type="text" value="{{ old('cta_title_white', $dha->cta_title_white) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
    </div>
    <div><label class="block text-sm mb-1">Description (one line per paragraph)</label><textarea name="cta_description" rows="3" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm">{{ old('cta_description', $dha->cta_description) }}</textarea></div>
    <div class="grid md:grid-cols-2 gap-4">
        <div><label class="block text-sm mb-1">Primary button</label><input name="cta_btn_primary_label" type="text" value="{{ old('cta_btn_primary_label', $dha->cta_btn_primary_label) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
        <div><label class="block text-sm mb-1">Primary URL</label><input name="cta_btn_primary_url" type="text" value="{{ old('cta_btn_primary_url', $dha->cta_btn_primary_url) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
        <div><label class="block text-sm mb-1">Secondary button</label><input name="cta_btn_secondary_label" type="text" value="{{ old('cta_btn_secondary_label', $dha->cta_btn_secondary_label) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
        <div><label class="block text-sm mb-1">Secondary URL</label><input name="cta_btn_secondary_url" type="text" value="{{ old('cta_btn_secondary_url', $dha->cta_btn_secondary_url) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
    </div>
</div>
