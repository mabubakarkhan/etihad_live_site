@php
    $idx = $index;
    $rid = $rate['id'] ?? null;
    $fileNumber = $rate['file_number'] ?? '';
    $plotSize = $rate['plot_size'] ?? '';
    $category = $rate['category'] ?? 'residential';
    $fileType = $rate['file_type'] ?? '';
    $phaseId = $rate['dha_phase_id'] ?? '';
    $price = $rate['price'] ?? '';
    $priceDigits = $rate['price_digits'] ?? '';
    $isActive = array_key_exists('is_active', $rate) ? (bool) $rate['is_active'] : true;
    $categoryOptions = $categoryOptions ?? \App\Models\DhaFileRate::categoryOptions();
@endphp
<tr class="rate-row border-b border-slate-100 dark:border-slate-800/80 align-top">
    <td class="py-2 px-1">
        <button type="button" class="rate-handle inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800" title="Drag to reorder" aria-label="Drag to reorder">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path d="M7 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm0 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm0 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm8-12a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm0 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm0 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"/></svg>
        </button>
    </td>
    <td class="py-2 px-2 min-w-[110px]">
        @if($rid)
            <input type="hidden" name="rates[{{ $idx }}][id]" value="{{ $rid }}" />
        @endif
        <input type="text" name="rates[{{ $idx }}][file_number]" value="{{ $fileNumber }}" placeholder="Admin only" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-2.5 py-2 text-sm" title="Internal file number — not shown on website" />
    </td>
    <td class="py-2 px-2 min-w-[150px]">
        <select name="rates[{{ $idx }}][dha_phase_id]" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-2.5 py-2 text-sm">
            <option value="">— Select phase —</option>
            @foreach(($dhaPhases ?? []) as $dp)
                <option value="{{ $dp->id }}" @selected((string) $phaseId === (string) $dp->id)>{{ $dp->title }}</option>
            @endforeach
        </select>
    </td>
    <td class="py-2 px-2 min-w-[110px]">
        <input type="text" name="rates[{{ $idx }}][plot_size]" value="{{ $plotSize }}" required placeholder="e.g. 5 Marla" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-2.5 py-2 text-sm" />
    </td>
    <td class="py-2 px-2 min-w-[120px]">
        <select name="rates[{{ $idx }}][category]" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-2.5 py-2 text-sm">
            @foreach($categoryOptions as $value => $label)
                <option value="{{ $value }}" @selected((string) $category === (string) $value)>{{ $label }}</option>
            @endforeach
        </select>
    </td>
    <td class="py-2 px-2 min-w-[130px]">
        <input
            type="text"
            name="rates[{{ $idx }}][file_type]"
            value="{{ $fileType }}"
            list="dha-file-type-suggestions"
            placeholder="e.g. Allocation"
            autocomplete="off"
            class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-2.5 py-2 text-sm"
        />
    </td>
    <td class="py-2 px-2 min-w-[110px]">
        <input type="text" name="rates[{{ $idx }}][price]" value="{{ $price }}" placeholder="e.g. 58.5 Lac" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-2.5 py-2 text-sm" />
    </td>
    <td class="py-2 px-2 min-w-[120px]">
        <input type="text" name="rates[{{ $idx }}][price_digits]" value="{{ $priceDigits }}" placeholder="e.g. 5,850,000" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-2.5 py-2 text-sm" />
    </td>
    <td class="py-2 px-2 text-center">
        <input type="hidden" name="rates[{{ $idx }}][is_active]" value="0" />
        <input type="checkbox" name="rates[{{ $idx }}][is_active]" value="1" class="rounded border-slate-400" {{ $isActive ? 'checked' : '' }} />
    </td>
    <td class="py-2 px-2 text-right">
        <button type="button" class="remove-rate-row text-xs text-rose-600 dark:text-rose-400 hover:underline">Remove</button>
    </td>
</tr>
