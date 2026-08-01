<div class="space-y-1.5">
    <label class="block text-sm">Meta title</label>
    <input name="meta_title" type="text" value="{{ old('meta_title', $record->meta_title) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
</div>
<div class="space-y-1.5">
    <label class="block text-sm">Meta description</label>
    <textarea name="meta_description" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('meta_description', $record->meta_description) }}</textarea>
</div>
<div class="space-y-1.5">
    <label class="block text-sm">Meta keywords</label>
    <input name="meta_keywords" type="text" value="{{ old('meta_keywords', $record->meta_keywords) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="space-y-1.5">
        <label class="block text-sm">Canonical URL</label>
        <input name="canonical_url" type="text" value="{{ old('canonical_url', $record->canonical_url) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
    </div>
    <div class="space-y-1.5">
        <label class="block text-sm">Robots</label>
        <input name="meta_robots" type="text" value="{{ old('meta_robots', $record->meta_robots) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="index, follow" />
    </div>
</div>
<div class="space-y-1.5">
    <label class="block text-sm">OG title</label>
    <input name="og_title" type="text" value="{{ old('og_title', $record->og_title) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
</div>
<div class="space-y-1.5">
    <label class="block text-sm">OG description</label>
    <textarea name="og_description" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('og_description', $record->og_description) }}</textarea>
</div>
<div class="space-y-1.5">
    <label class="block text-sm">OG image URL</label>
    <input name="og_image" type="text" value="{{ old('og_image', $record->og_image) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="https://..." />
</div>
