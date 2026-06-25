@php
    $gallery = old('image_gallery_paths', collect($phase->image_gallery ?? [])->pluck('path')->filter()->values()->all());
    $plotMaps = old('plot_map_paths') !== null
        ? collect(old('plot_map_paths'))->map(fn ($p, $i) => ['path' => $p, 'title' => old('plot_map_titles.'.$i, '')])->all()
        : ($phase->plot_maps ?? []);
    $videos = old('video_gallery', $phase->video_gallery ?? []);
    $videos = is_array($videos) ? $videos : [];
    $phaseId = $phase->id ?? null;
    $featuredPath = old('featured_image_path', '');
    $existingFeatured = (!$featuredPath && ($phase->featured_image ?? null)) ? $phase->featured_image : '';
    $cardPath = old('card_image_path', '');
    $existingCard = (!$cardPath && ($phase->card_image ?? null)) ? $phase->card_image : '';
    $pdfPath = old('phase_pdf_path', '');
    $existingPdf = (!$pdfPath && ($phase->phase_pdf ?? null)) ? $phase->phase_pdf : '';
@endphp

@if(!empty($uploadToken))
    <input type="hidden" id="dha-phase-upload-token" value="{{ $uploadToken }}" />
@endif

<div class="space-y-6 max-w-4xl">
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Basics</h2>
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="block text-sm mb-1">Title *</label>
                <input name="title" type="text" value="{{ old('title', $phase->title) }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-sm mb-1">Slug</label>
                <input name="slug" type="text" value="{{ old('slug', $phase->slug) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-sm mb-1">Sort order</label>
                <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $phase->sort_order) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-sm mb-1">Status</label>
                <select name="status" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm">
                    <option value="active" @selected(old('status', $phase->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $phase->status) === 'inactive')>Inactive</option>
                </select></div>
        </div>
        <div><label class="block text-sm mb-1">Description / Overview (rich text)</label>
            <div class="richtext-wrap bg-slate-50 dark:bg-slate-950/60 rounded-lg border border-slate-300 dark:border-slate-700 min-h-[180px]">
                <textarea name="description" id="dha_phase_description" class="hidden">{{ old('description', $phase->description) }}</textarea>
            </div></div>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Hero section (phase detail page)</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400">Powers the luxury hero: tagline, stat cards, and section content below the hero nav.</p>
        <div><label class="block text-sm mb-1">Hero tagline</label>
            <textarea name="hero_lead" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" placeholder="A perfect blend of prime location, modern infrastructure, and high investment potential.">{{ old('hero_lead', $phase->hero_lead) }}</textarea></div>
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="block text-sm mb-1">Prime location (value)</label>
                <input name="stat_location" type="text" value="{{ old('stat_location', $phase->stat_location) }}" placeholder="Lahore, Pakistan" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-sm mb-1">Total area (value)</label>
                <input name="stat_total_area" type="text" value="{{ old('stat_total_area', $phase->stat_total_area) }}" placeholder="5,987 Kanal" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-sm mb-1">Total plots (value)</label>
                <input name="stat_total_plots" type="text" value="{{ old('stat_total_plots', $phase->stat_total_plots) }}" placeholder="54,541+" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-sm mb-1">Year developed (value)</label>
                <input name="stat_year_developed" type="text" value="{{ old('stat_year_developed', $phase->stat_year_developed) }}" placeholder="2002" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
        </div>
        <div><label class="block text-sm mb-1">Features section (rich text)</label>
            <div class="richtext-wrap bg-slate-50 dark:bg-slate-950/60 rounded-lg border border-slate-300 dark:border-slate-700 min-h-[140px]">
                <textarea name="features_content" id="dha_phase_features" class="hidden">{{ old('features_content', $phase->features_content) }}</textarea>
            </div></div>
        <div><label class="block text-sm mb-1">Market insights (rich text)</label>
            <div class="richtext-wrap bg-slate-50 dark:bg-slate-950/60 rounded-lg border border-slate-300 dark:border-slate-700 min-h-[140px]">
                <textarea name="market_insights" id="dha_phase_market" class="hidden">{{ old('market_insights', $phase->market_insights) }}</textarea>
            </div></div>
        <div><label class="block text-sm mb-1">Contact intro</label>
            <textarea name="contact_intro" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" placeholder="Speak with our DHA specialists…">{{ old('contact_intro', $phase->contact_intro) }}</textarea></div>

        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/40 p-4 space-y-3">
            <h3 class="text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Hero action buttons</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">PDF, VR, and Map buttons appear prominently on the phase page hero when configured.</p>
            <div>
                <label class="block text-sm mb-1">VR tour URL</label>
                <input name="vr_tour_url" type="url" value="{{ old('vr_tour_url', $phase->vr_tour_url) }}" placeholder="https://…" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                <p class="text-xs text-slate-500 mt-1">Shows a VR button when filled. Opens in a new tab.</p>
            </div>
            <div>
                <label class="block text-sm mb-1">Phase brochure (PDF)</label>
                <input type="hidden" name="phase_pdf_path" id="dha-phase-pdf-path" value="{{ $pdfPath }}" />
                @if($existingPdf)
                <div id="dha-phase-pdf-existing" class="mb-2 flex flex-wrap items-center gap-3">
                    <a href="{{ asset('storage/' . ltrim($existingPdf, '/')) }}" target="_blank" rel="noopener" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">Current PDF</a>
                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer">
                        <input type="checkbox" name="remove_phase_pdf" value="1" class="rounded border-slate-400" /> Remove PDF
                    </label>
                </div>
                @else
                <div id="dha-phase-pdf-existing" class="hidden mb-2"></div>
                @endif
                <div id="dha-phase-pdf-preview" class="{{ $pdfPath ? '' : 'hidden' }} mb-2 text-sm text-emerald-600 dark:text-emerald-400"></div>
                <div id="dha-phase-pdf-upload-status" class="hidden mb-2 rounded-lg border border-sky-500/40 bg-sky-500/10 px-3 py-2 text-sm text-sky-800 dark:text-sky-200">Uploading PDF…</div>
                <div id="dha-phase-pdf-upload-success" class="hidden mb-2 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-sm text-emerald-800 dark:text-emerald-200"></div>
                <div id="dha-phase-pdf-upload-error" class="hidden mb-2 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200"></div>
                <input type="file" accept=".pdf,application/pdf" class="dha-media-upload block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" data-upload-type="phase_pdf" data-path-name="phase_pdf_path" data-status-prefix="dha-phase-pdf" />
            </div>
        </div>
    </div>

    @php
        $valueProps = old('value_props', $phase->valuePropositions());
        $attractionItems = old('attractions', $phase->attractions());
        $investItems = old('invest_reasons', $phase->investmentReasons());
        $highlights = array_merge($phase->projectHighlights(), old('highlights', []));
        if (old('highlight_tag_primary') !== null) {
            $highlights = array_merge($highlights, [
                'tag_primary' => old('highlight_tag_primary'),
                'tag_secondary' => old('highlight_tag_secondary'),
                'location' => old('highlight_location'),
                'total_views' => old('highlight_total_views'),
                'developed_year' => old('highlight_developed_year'),
                'register_title' => old('highlight_register_title'),
                'register_text' => old('highlight_register_text'),
                'register_url' => old('highlight_register_url'),
            ]);
        }
    @endphp

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Sections below hero</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400">Value bar, attractions grid, investment block, and help bar. Leave blank to use defaults for this phase.</p>

        <div class="space-y-3">
            <h3 class="text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Value proposition bar (4 items)</h3>
            @foreach(range(0, 3) as $i)
                @php $vp = $valueProps[$i] ?? ['title' => '', 'text' => '', 'icon' => '']; @endphp
                <div class="grid md:grid-cols-3 gap-2 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800">
                    <input name="value_props[{{ $i }}][title]" type="text" value="{{ $vp['title'] ?? '' }}" placeholder="Title" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                    <input name="value_props[{{ $i }}][text]" type="text" value="{{ $vp['text'] ?? '' }}" placeholder="Description" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                    <input name="value_props[{{ $i }}][icon]" type="text" value="{{ $vp['icon'] ?? '' }}" placeholder="Lucide icon (e.g. map-pin)" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                </div>
            @endforeach
        </div>

        <div class="space-y-3">
            <div><label class="block text-sm mb-1">Attractions heading</label>
                <input name="attractions_heading" type="text" value="{{ old('attractions_heading', $phase->attractions_heading) }}" placeholder="ATTRACTIONS NEAR DHA PHASE 1" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <h3 class="text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Attractions grid (6 items)</h3>
            @foreach(range(0, 5) as $i)
                @php $at = $attractionItems[$i] ?? ['title' => '', 'text' => '', 'icon' => '', 'image' => '']; @endphp
                <div class="grid md:grid-cols-4 gap-2 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800">
                    <input name="attractions[{{ $i }}][title]" type="text" value="{{ $at['title'] ?? '' }}" placeholder="Title" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                    <input name="attractions[{{ $i }}][text]" type="text" value="{{ $at['text'] ?? '' }}" placeholder="Description" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                    <input name="attractions[{{ $i }}][icon]" type="text" value="{{ $at['icon'] ?? '' }}" placeholder="Lucide icon" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                    <input name="attractions[{{ $i }}][image]" type="text" value="{{ $at['image'] ?? '' }}" placeholder="Image path (optional)" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                </div>
            @endforeach
        </div>

        <div class="space-y-3">
            <h3 class="text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Why invest (6 items)</h3>
            @foreach(range(0, 5) as $i)
                @php $ir = $investItems[$i] ?? ['title' => '', 'text' => '', 'icon' => '']; @endphp
                <div class="grid md:grid-cols-3 gap-2 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800">
                    <input name="invest_reasons[{{ $i }}][title]" type="text" value="{{ $ir['title'] ?? '' }}" placeholder="Title" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                    <input name="invest_reasons[{{ $i }}][text]" type="text" value="{{ $ir['text'] ?? '' }}" placeholder="Description" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                    <input name="invest_reasons[{{ $i }}][icon]" type="text" value="{{ $ir['icon'] ?? '' }}" placeholder="Lucide icon" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 px-3 py-2 text-sm" />
                </div>
            @endforeach
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <h3 class="md:col-span-2 text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Project highlights card</h3>
            <div><label class="block text-xs mb-1">Tag 1</label><input name="highlight_tag_primary" type="text" value="{{ $highlights['tag_primary'] ?? '' }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-xs mb-1">Tag 2</label><input name="highlight_tag_secondary" type="text" value="{{ $highlights['tag_secondary'] ?? '' }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div class="md:col-span-2"><label class="block text-xs mb-1">Location line</label><input name="highlight_location" type="text" value="{{ $highlights['location'] ?? '' }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-xs mb-1">Total views value</label><input name="highlight_total_views" type="text" value="{{ $highlights['total_views'] ?? '' }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-xs mb-1">Developed year</label><input name="highlight_developed_year" type="text" value="{{ $highlights['developed_year'] ?? '' }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-xs mb-1">Register title</label><input name="highlight_register_title" type="text" value="{{ $highlights['register_title'] ?? '' }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-xs mb-1">Register URL</label><input name="highlight_register_url" type="text" value="{{ $highlights['register_url'] ?? '#dha-contact' }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div class="md:col-span-2"><label class="block text-xs mb-1">Register description</label><textarea name="highlight_register_text" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm">{{ $highlights['register_text'] ?? '' }}</textarea></div>
        </div>

        <div class="grid md:grid-cols-1 gap-4">
            <h3 class="text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Help bar (marble)</h3>
            <div><label class="block text-xs mb-1">Eyebrow</label><input name="help_bar_eyebrow" type="text" value="{{ old('help_bar_eyebrow', $phase->help_bar_eyebrow) }}" placeholder="HAVE QUESTIONS?" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-xs mb-1">Title</label><input name="help_bar_title" type="text" value="{{ old('help_bar_title', $phase->help_bar_title) }}" placeholder="We're Here to Help!" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-xs mb-1">Description</label><textarea name="help_bar_text" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" placeholder="Connect with our property experts…">{{ old('help_bar_text', $phase->help_bar_text) }}</textarea></div>
            <p class="text-xs text-slate-500">Phone &amp; WhatsApp buttons use site Contact Settings.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4" data-media-wrap>
        <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Banner image (phase page hero — right panel)</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400">Recommended <strong>1200×900px</strong> or larger portrait/landscape photo for the hero right panel (villa / aerial).</p>
        <input type="hidden" name="featured_image_path" id="dha-phase-featured-path" value="{{ $featuredPath }}" />
        <div id="dha-phase-featured-existing" class="{{ $existingFeatured && !$featuredPath ? '' : 'hidden' }} mb-2 flex items-center gap-3 flex-wrap">
            @if($existingFeatured)
                <img src="{{ asset('storage/' . $existingFeatured) }}" alt="" class="dha-featured-preview-img h-20 rounded border border-slate-300 dark:border-slate-700 object-cover" />
            @endif
            <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer">
                <input type="checkbox" name="remove_featured_image" value="1" class="rounded border-slate-400" {{ old('remove_featured_image') ? 'checked' : '' }} /> Remove current image
            </label>
        </div>
        <div id="dha-phase-featured-preview" class="{{ $featuredPath ? '' : 'hidden' }} mb-2 flex items-center gap-3 flex-wrap">
            @if($featuredPath)
                <img src="{{ asset('storage/' . $featuredPath) }}" alt="" class="dha-featured-preview-img h-20 rounded border border-slate-300 dark:border-slate-700 object-cover" />
            @else
                <img src="" alt="" class="dha-featured-preview-img h-20 rounded border border-slate-300 dark:border-slate-700 object-cover hidden" />
            @endif
        </div>
        <div id="dha-phase-featured-upload-status" class="hidden mb-2 rounded-lg border border-sky-500/40 bg-sky-500/10 px-3 py-2 text-sm text-sky-800 dark:text-sky-200">
            <span class="inline-block w-3 h-3 border-2 border-sky-600 border-t-transparent rounded-full animate-spin mr-2 align-middle"></span>
            Uploading featured image, please wait…
        </div>
        <div id="dha-phase-featured-upload-success" class="hidden mb-2 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-sm text-emerald-800 dark:text-emerald-200"></div>
        <div id="dha-phase-featured-upload-error" class="hidden mb-2 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200"></div>
        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Banner image — 1920×600px recommended (uploads immediately)</label>
        <input type="file" accept="image/*" class="dha-media-upload block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" data-upload-type="featured" data-path-name="featured_image_path" data-status-prefix="dha-phase-featured" />
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4" data-media-wrap>
        <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Card image (homepage &amp; DHA listing boxes)</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400">Square image for phase cards. Recommended <strong>800×800px</strong> (1:1). If empty, the banner image is used on cards until you upload a card image.</p>
        <input type="hidden" name="card_image_path" id="dha-phase-card-path" value="{{ $cardPath }}" />
        <div id="dha-phase-card-existing" class="{{ $existingCard && !$cardPath ? '' : 'hidden' }} mb-2 flex items-center gap-3 flex-wrap">
            @if($existingCard)
                <img src="{{ asset('storage/' . $existingCard) }}" alt="" class="dha-card-preview-img h-20 w-20 rounded border border-slate-300 dark:border-slate-700 object-cover" />
            @endif
            <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer">
                <input type="checkbox" name="remove_card_image" value="1" class="rounded border-slate-400" {{ old('remove_card_image') ? 'checked' : '' }} /> Remove current card image
            </label>
        </div>
        <div id="dha-phase-card-preview" class="{{ $cardPath ? '' : 'hidden' }} mb-2 flex items-center gap-3 flex-wrap">
            @if($cardPath)
                <img src="{{ asset('storage/' . $cardPath) }}" alt="" class="dha-card-preview-img h-20 w-20 rounded border border-slate-300 dark:border-slate-700 object-cover" />
            @else
                <img src="" alt="" class="dha-card-preview-img h-20 w-20 rounded border border-slate-300 dark:border-slate-700 object-cover hidden" />
            @endif
        </div>
        <div id="dha-phase-card-upload-status" class="hidden mb-2 rounded-lg border border-sky-500/40 bg-sky-500/10 px-3 py-2 text-sm text-sky-800 dark:text-sky-200">
            <span class="inline-block w-3 h-3 border-2 border-sky-600 border-t-transparent rounded-full animate-spin mr-2 align-middle"></span>
            Uploading card image, please wait…
        </div>
        <div id="dha-phase-card-upload-success" class="hidden mb-2 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-sm text-emerald-800 dark:text-emerald-200"></div>
        <div id="dha-phase-card-upload-error" class="hidden mb-2 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200"></div>
        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Card image — 800×800px recommended (uploads immediately)</label>
        <input type="file" accept="image/*" class="dha-media-upload block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" data-upload-type="card" data-path-name="card_image_path" data-status-prefix="dha-phase-card" data-preview-class="dha-card-preview-img" />
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Map</h2>
        <div class="grid md:grid-cols-3 gap-4">
            <div><label class="block text-sm mb-1">Latitude</label><input name="latitude" type="text" value="{{ old('latitude', $phase->latitude) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-sm mb-1">Longitude</label><input name="longitude" type="text" value="{{ old('longitude', $phase->longitude) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-sm mb-1">Zoom</label><input name="map_zoom" type="number" min="1" max="21" value="{{ old('map_zoom', $phase->map_zoom ?? 14) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
        </div>
        <div><label class="block text-sm mb-1">Google Map embed / URL</label>
            <textarea name="google_map" rows="3" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm">{{ old('google_map', $phase->google_map) }}</textarea></div>
        <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
            <input type="checkbox" name="show_map_button" value="1" @checked(old('show_map_button', $phase->show_map_button)) class="rounded border-slate-400" />
            Show <strong>View Map</strong> button on phase hero
        </label>
        <p class="text-xs text-slate-500 dark:text-slate-400">Button appears only when checked and map data exists (coordinates, Google embed, or plot maps).</p>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Interactive map preview (portal section)</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400">Shown on the phase page before the gallery. Clicking the image opens the map link in a new tab.</p>
        <div>
            <label class="block text-sm mb-1">Heading</label>
            <input type="text" name="map_section_heading" value="{{ old('map_section_heading', $phase->map_section_heading) }}" placeholder="e.g. Master Plan Map" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="block text-sm mb-1">Tagline</label>
            <input type="text" name="map_section_tagline" value="{{ old('map_section_tagline', $phase->map_section_tagline) }}" placeholder="e.g. Explore {{ $phase->title }} in detail" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="block text-sm mb-1">Map link URL</label>
            <input type="url" name="map_section_url" value="{{ old('map_section_url', $phase->map_section_url) }}" placeholder="https://..." class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" />
        </div>
        @php $mapSectionPath = old('map_section_image_path', $phase->map_section_image); @endphp
        <div id="dha-phase-map-section-preview" class="{{ $mapSectionPath ? '' : 'hidden' }} mb-2">
            @if($mapSectionPath)
                <img src="{{ asset('storage/' . ltrim($mapSectionPath, '/')) }}" alt="" class="dha-map-section-preview-img max-h-40 rounded-lg border border-slate-300 dark:border-slate-700" />
            @else
                <img src="" alt="" class="dha-map-section-preview-img max-h-40 rounded-lg border border-slate-300 dark:border-slate-700 hidden" />
            @endif
        </div>
        <div id="dha-phase-map-section-upload-status" class="hidden mb-2 rounded-lg border border-sky-500/40 bg-sky-500/10 px-3 py-2 text-sm text-sky-800 dark:text-sky-200">
            <span class="inline-block w-3 h-3 border-2 border-sky-600 border-t-transparent rounded-full animate-spin mr-2 align-middle"></span>
            Uploading preview image, please wait…
        </div>
        <div id="dha-phase-map-section-upload-success" class="hidden mb-2 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-sm text-emerald-800 dark:text-emerald-200"></div>
        <div id="dha-phase-map-section-upload-error" class="hidden mb-2 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200"></div>
        <input type="hidden" name="map_section_image_path" value="{{ $mapSectionPath }}" />
        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Preview image (upload immediately)</label>
        <input type="file" accept="image/*" class="dha-media-upload block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" data-upload-type="map_section" data-path-name="map_section_image_path" data-status-prefix="dha-phase-map-section" data-preview-class="dha-map-section-preview-img" />
        <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
            <input type="checkbox" name="remove_map_section_image" value="1" @checked(old('remove_map_section_image')) class="rounded border-slate-400" />
            Remove current preview image
        </label>
        <div class="pt-4 border-t border-slate-200 dark:border-slate-700 space-y-3">
            <h3 class="text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Map viewer SEO</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Meta tags for the interactive map page. Leave blank to auto-generate from heading and tagline.</p>
            <div>
                <label class="block text-sm mb-1">Meta title</label>
                <input type="text" name="map_section_meta_title" value="{{ old('map_section_meta_title', $phase->map_section_meta_title) }}" placeholder="e.g. {{ $phase->title }} Master Plan Map | Etihad Marketing" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="block text-sm mb-1">Meta description</label>
                <textarea name="map_section_meta_description" rows="2" placeholder="Short description for search engines (max 500 chars)" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm">{{ old('map_section_meta_description', $phase->map_section_meta_description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm mb-1">Meta keywords</label>
                <input type="text" name="map_section_meta_keywords" value="{{ old('map_section_meta_keywords', $phase->map_section_meta_keywords) }}" placeholder="DHA phase map, master plan, Lahore plots" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" />
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Property types (prominent on phase page)</h2>
        <p class="text-xs text-slate-500">Link categories like Residential, Commercial, Plaza plot.</p>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-2">
            @foreach($projectTypes as $pt)
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" name="project_type_ids[]" value="{{ $pt->id }}" @checked(in_array($pt->id, old('project_type_ids', $selectedTypeIds ?? []))) class="rounded" />
                    {{ $pt->name }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Image gallery</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400">Recommended <strong>1200×800px</strong> (3:2) per image. Displayed in phase gallery grid.</p>
        <div id="dha-phase-gallery-list" class="flex flex-wrap gap-3">
            @foreach($gallery as $path)
                <div class="dha-gallery-item relative flex items-center gap-2" data-path="{{ $path }}">
                    <img src="{{ asset('storage/' . ltrim($path, '/')) }}" class="h-20 w-20 object-cover rounded-lg border border-slate-300 dark:border-slate-700" alt="" />
                    <button type="button" class="dha-remove-gallery text-xs px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-400 hover:bg-rose-600/10">Remove</button>
                    <input type="hidden" name="image_gallery_paths[]" value="{{ $path }}" />
                </div>
            @endforeach
        </div>
        <div id="dha-phase-gallery-upload-status" class="hidden mb-2 rounded-lg border border-sky-500/40 bg-sky-500/10 px-3 py-2 text-sm text-sky-800 dark:text-sky-200">
            <span class="inline-block w-3 h-3 border-2 border-sky-600 border-t-transparent rounded-full animate-spin mr-2 align-middle"></span>
            <span id="dha-phase-gallery-upload-status-text">Uploading…</span>
        </div>
        <div id="dha-phase-gallery-upload-error" class="hidden mb-2 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200"></div>
        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Gallery images — 1200×800px recommended each (upload immediately, append only)</label>
        <input type="file" accept="image/*" multiple class="dha-media-upload block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" data-upload-type="image_gallery" />
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Video gallery</h2>
        <div id="dha-phase-videos" class="space-y-2">
            @forelse($videos as $vi => $video)
                <textarea name="video_gallery[]" rows="2" placeholder="YouTube embed code" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm">{{ $video }}</textarea>
            @empty
                <textarea name="video_gallery[]" rows="2" placeholder="YouTube embed code" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm"></textarea>
            @endforelse
        </div>
        <button type="button" id="dha-phase-add-video" class="text-xs text-sky-600 hover:underline">+ Add video</button>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">Plot maps (zoomable)</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400">Recommended <strong>2000×1400px</strong> or larger (high resolution). Users open full size with zoom — export sharp, readable map scans.</p>
        <div id="dha-phase-plot-maps" class="space-y-3">
            @forelse($plotMaps as $pm)
                <div class="dha-plot-item flex flex-wrap items-end gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                    <img src="{{ asset('storage/' . ltrim($pm['path'] ?? '', '/')) }}" class="h-16 w-16 object-cover rounded plot-map-thumb" alt="" />
                    <input type="hidden" name="plot_map_paths[]" value="{{ $pm['path'] ?? '' }}" />
                    <input type="text" name="plot_map_titles[]" value="{{ $pm['title'] ?? '' }}" placeholder="Map title" class="flex-1 min-w-[140px] rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" />
                    <button type="button" class="dha-remove-plot text-xs text-rose-600">Remove</button>
                </div>
            @empty
            @endforelse
        </div>
        <div id="dha-phase-plot-upload-status" class="hidden mb-2 rounded-lg border border-sky-500/40 bg-sky-500/10 px-3 py-2 text-sm text-sky-800 dark:text-sky-200">
            <span class="inline-block w-3 h-3 border-2 border-sky-600 border-t-transparent rounded-full animate-spin mr-2 align-middle"></span>
            <span id="dha-phase-plot-upload-status-text">Uploading…</span>
        </div>
        <div id="dha-phase-plot-upload-error" class="hidden mb-2 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200"></div>
        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Plot map images — 2000×1400px+ recommended (upload immediately)</label>
        <input type="file" accept="image/*" multiple class="dha-media-upload block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-2 file:rounded file:border-0 file:bg-slate-200 dark:file:bg-slate-700 file:px-3 file:py-1.5 file:text-slate-800 dark:file:text-slate-200" data-upload-type="plot_maps" />
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-emerald-600 uppercase tracking-wider">SEO</h2>
        <div class="space-y-4">
            <div><label class="block text-sm mb-1">Meta title</label><input name="meta_title" type="text" value="{{ old('meta_title', $phase->meta_title) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-sm mb-1">Meta description</label><textarea name="meta_description" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm">{{ old('meta_description', $phase->meta_description) }}</textarea></div>
            <div><label class="block text-sm mb-1">Meta keywords</label><input name="meta_keywords" type="text" value="{{ old('meta_keywords', $phase->meta_keywords) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
            <div><label class="block text-sm mb-1">Canonical URL</label><input name="canonical_url" type="text" value="{{ old('canonical_url', $phase->canonical_url) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm" /></div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('theme/js/admin-dha-media.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function bindQuill(textareaId) {
        var el = document.getElementById(textareaId);
        if (!el || typeof Quill === 'undefined') return;
        var q = new Quill(el.parentElement, { theme: 'snow' });
        q.root.innerHTML = el.value;
        el.closest('form').addEventListener('submit', function() { el.value = q.root.innerHTML; });
    }
    bindQuill('dha_phase_description');
    bindQuill('dha_phase_features');
    bindQuill('dha_phase_market');
    document.getElementById('dha-phase-add-video')?.addEventListener('click', function() {
        var ta = document.createElement('textarea');
        ta.name = 'video_gallery[]';
        ta.rows = 2;
        ta.placeholder = 'YouTube embed code';
        ta.className = 'block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm';
        document.getElementById('dha-phase-videos').appendChild(ta);
    });
});
</script>
@endpush
