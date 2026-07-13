<div id="dha-phase-bulk-media-modal" class="fixed inset-0 z-[200] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm dha-phase-bulk-media-backdrop"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
        <div class="relative w-full max-w-2xl rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-2xl my-8" role="dialog" aria-modal="true" aria-labelledby="dha-phase-bulk-media-title">
            <div class="flex items-start justify-between gap-3 border-b border-slate-200 dark:border-slate-800 px-5 py-4">
                <div>
                    <h2 id="dha-phase-bulk-media-title" class="text-lg font-semibold text-slate-900 dark:text-slate-50">Bulk Media</h2>
                    <p id="dha-phase-bulk-media-subtitle" class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Upload a ZIP with DHA phase images.</p>
                </div>
                <button type="button" class="dha-phase-bulk-media-close rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Close">&times;</button>
            </div>

            <div class="px-5 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
                <div id="dha-phase-bulk-media-step-upload">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                        Use folders: <code class="text-emerald-600 dark:text-emerald-400">featured</code>,
                        <code class="text-emerald-600 dark:text-emerald-400">card</code>,
                        <code class="text-emerald-600 dark:text-emerald-400">pdf</code>,
                        <code class="text-emerald-600 dark:text-emerald-400">gallery</code>,
                        <code class="text-emerald-600 dark:text-emerald-400">plot-maps</code>.
                        See <code>live.txt</code> for the full guide.
                    </p>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">ZIP file</label>
                    <input type="file" id="dha-phase-bulk-media-zip" accept=".zip,application/zip" class="block w-full text-sm text-slate-600 dark:text-slate-400 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-950 hover:file:bg-emerald-400" />
                </div>

                <div id="dha-phase-bulk-media-status" class="hidden rounded-lg border px-3 py-2 text-sm"></div>

                <div id="dha-phase-bulk-media-preview" class="hidden space-y-3">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Import preview</h3>
                    <ul id="dha-phase-bulk-media-preview-list" class="space-y-2 text-sm"></ul>
                    <div id="dha-phase-bulk-media-warnings" class="hidden rounded-lg border border-amber-500/40 bg-amber-500/10 px-3 py-2 text-xs text-amber-900 dark:text-amber-200"></div>
                </div>

                <div id="dha-phase-bulk-media-result" class="hidden space-y-2 text-sm"></div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-200 dark:border-slate-800 px-5 py-4">
                <button type="button" class="dha-phase-bulk-media-close inline-flex items-center rounded-lg border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Cancel</button>
                <button type="button" id="dha-phase-bulk-media-upload-btn" class="inline-flex items-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400 shadow shadow-emerald-500/30">Upload &amp; preview</button>
                <button type="button" id="dha-phase-bulk-media-import-btn" class="hidden inline-flex items-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400 shadow shadow-emerald-500/30">Confirm import</button>
            </div>
        </div>
    </div>
</div>
