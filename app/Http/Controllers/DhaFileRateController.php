<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DhaFileRate;
use App\Models\DhaFileRateSetting;
use App\Models\DhaPhase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DhaFileRateController extends Controller
{
    public function edit(): View
    {
        $setting = DhaFileRateSetting::instance();
        $rates = DhaFileRate::query()->frontOrdered()->get();
        $dhaPhases = DhaPhase::frontOrdered()->get(['id', 'title', 'slug']);
        $typeSuggestions = DhaFileRate::typeSuggestions();
        $categoryOptions = DhaFileRate::categoryOptions();

        return view('admin.dha_file_rates.edit', compact(
            'setting',
            'rates',
            'dhaPhases',
            'typeSuggestions',
            'categoryOptions'
        ));
    }

    public function update(Request $request)
    {
        $setting = DhaFileRateSetting::instance();

        $validated = $request->validate([
            'heading' => ['required', 'string', 'max:255'],
            'subheading' => ['nullable', 'string', 'max:500'],
            'details' => ['nullable', 'string', 'max:5000'],
            'is_published' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'meta_robots' => ['nullable', 'string', 'max:120'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'twitter_card' => ['nullable', 'string', 'max:80'],
            'rates' => ['nullable', 'array', 'max:200'],
            'rates.*.id' => ['nullable', 'integer', 'exists:dha_file_rates,id'],
            'rates.*.file_number' => ['nullable', 'string', 'max:120'],
            'rates.*.plot_size' => ['required', 'string', 'max:120'],
            'rates.*.category' => ['nullable', 'string', 'in:residential,commercial'],
            'rates.*.file_type' => ['nullable', 'string', 'max:120'],
            'rates.*.dha_phase_id' => ['nullable', 'integer', 'exists:dha_phases,id'],
            'rates.*.price' => ['nullable', 'string', 'max:120'],
            'rates.*.price_digits' => ['nullable', 'string', 'max:60'],
            'rates.*.is_active' => ['nullable', 'boolean'],
        ]);

        $setting->update([
            'heading' => $validated['heading'],
            'subheading' => $validated['subheading'] ?? null,
            'details' => $validated['details'] ?? null,
            'is_published' => $request->boolean('is_published'),
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'meta_robots' => $validated['meta_robots'] ?? null,
            'og_title' => $validated['og_title'] ?? null,
            'og_description' => $validated['og_description'] ?? null,
            'twitter_card' => $validated['twitter_card'] ?? null,
        ]);

        DB::transaction(function () use ($validated) {
            $keptIds = [];
            $rows = $validated['rates'] ?? [];

            foreach (array_values($rows) as $index => $row) {
                $rate = ! empty($row['id'])
                    ? DhaFileRate::query()->findOrFail($row['id'])
                    : new DhaFileRate();

                $fileType = trim((string) ($row['file_type'] ?? ''));
                $fileNumber = trim((string) ($row['file_number'] ?? ''));

                $rate->fill([
                    'file_number' => $fileNumber !== '' ? $fileNumber : null,
                    'plot_size' => $row['plot_size'],
                    'category' => $row['category'] ?: DhaFileRate::CATEGORY_RESIDENTIAL,
                    'file_type' => $fileType !== '' ? $fileType : null,
                    'dha_phase_id' => $row['dha_phase_id'] ?: null,
                    'price' => $row['price'] ?? null,
                    'price_digits' => $row['price_digits'] ?? null,
                    'is_active' => filter_var($row['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'sort_order' => $index + 1,
                ]);
                $rate->save();
                $keptIds[] = $rate->id;
            }

            if (empty($keptIds)) {
                DhaFileRate::query()->delete();
            } else {
                DhaFileRate::query()->whereNotIn('id', $keptIds)->delete();
            }
        });

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'dha_file_rates_updated', 'DHA File Rates page and listings updated.');
        }

        return redirect()
            ->route('admin.dha-file-rates.edit')
            ->with('status', 'DHA File Rates saved. Page content, SEO, prices, and order updated.');
    }

    public function show(): View
    {
        $setting = DhaFileRateSetting::instance();
        abort_unless($setting->is_published, 404);

        $rates = DhaFileRate::query()
            ->active()
            ->frontOrdered()
            ->with(['dhaPhase:id,title,slug,sort_order'])
            ->get();

        // Group by DHA phase (phase sort order), keep rate order inside each phase
        $groupedRates = $rates
            ->sortBy(function (DhaFileRate $rate) {
                $phaseOrder = $rate->dhaPhase?->sort_order ?? 9999;
                $phaseId = $rate->dha_phase_id ?? 999999;

                return sprintf('%06d-%06d-%06d', $phaseOrder, $phaseId, $rate->sort_order ?? 0);
            })
            ->groupBy(fn (DhaFileRate $rate) => $rate->dha_phase_id ?: 0);

        $phasesForFilter = DhaPhase::query()
            ->whereIn('id', DhaFileRate::query()->active()->whereNotNull('dha_phase_id')->distinct()->pluck('dha_phase_id'))
            ->frontOrdered()
            ->get(['id', 'title', 'slug']);

        $contact = \App\Models\ContactSetting::instance();
        $callPhone = preg_replace('/\s+/', '', (string) ($contact->phone ?: ''));
        $whatsappPhone = preg_replace('/\D+/', '', (string) ($contact->whatsapp ?: $contact->phone ?: ''));

        $pageTitle = $setting->meta_title ?: ($setting->heading ?: 'DHA File Rates');

        return view('dha-file-rates', compact(
            'setting',
            'rates',
            'groupedRates',
            'phasesForFilter',
            'pageTitle',
            'callPhone',
            'whatsappPhone'
        ));
    }
}
