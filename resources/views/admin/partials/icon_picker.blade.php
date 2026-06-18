{{-- Global icon picker modal. Include once per page. Buttons with class "icon-picker-btn" and data-target="input_id" open this and set the input value on select. --}}
<div id="icon-picker-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/70 dark:bg-slate-950/80 backdrop-blur-sm" data-dismiss="icon-picker-modal"></div>
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl max-h-[85vh] flex flex-col rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-2xl overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-2">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Choose icon</h3>
            <button type="button" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700" data-dismiss="icon-picker-modal" aria-label="Close">&times;</button>
        </div>
        <div class="p-3 border-b border-slate-200 dark:border-slate-700">
            <input type="text" id="icon-picker-search" placeholder="Search property icons (home, bed, parking, key…)" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-950 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400" />
        </div>
        <div id="icon-picker-list" class="flex-1 overflow-y-auto p-3 grid grid-cols-4 sm:grid-cols-6 gap-2">
            @foreach(real_estate_icon_picker_options() as $icon)
                @php
                    $ref = $icon['ref'];
                    $label = $icon['label'];
                    $keywords = $icon['keywords'] ?? '';
                    $iconUrl = iconify_url($ref, 24);
                @endphp
                <button type="button" class="icon-picker-item flex flex-col items-center justify-center p-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 hover:bg-emerald-500/10 hover:border-emerald-500/40 dark:hover:bg-emerald-500/10 transition text-slate-700 dark:text-slate-300" data-icon="{{ $ref }}" data-search="{{ strtolower($ref . ' ' . $label . ' ' . $keywords) }}" title="{{ $ref }}">
                    <span class="icon-picker-icon-wrap h-6 w-6 mb-1 flex items-center justify-center rounded bg-slate-200 dark:bg-slate-700 text-slate-500">
                        <img src="{{ $iconUrl }}" alt="" class="h-5 w-5 opacity-90 icon-picker-img" loading="lazy" onerror="this.style.display='none';this.nextElementSibling&&(this.nextElementSibling.style.display='inline');" />
                        <span class="icon-picker-fallback text-xs" style="display:none">◆</span>
                    </span>
                    <span class="text-[10px] font-mono truncate w-full text-center">{{ $label }}</span>
                </button>
            @endforeach
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('icon-picker-modal');
    var search = document.getElementById('icon-picker-search');
    var list = document.getElementById('icon-picker-list');
    var currentTarget = null;

    if (!modal || !list) return;

    function openPicker(targetInputId) {
        currentTarget = document.getElementById(targetInputId) || (targetInputId && targetInputId.charAt(0) === '#' ? document.querySelector(targetInputId) : null);
        if (currentTarget) {
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            search.value = '';
            filterIcons();
            requestAnimationFrame(function() { search.focus(); });
        }
    }

    function closePicker() {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        currentTarget = null;
    }

    function filterIcons() {
        var q = (search && search.value || '').toLowerCase().trim();
        var items = list.querySelectorAll('.icon-picker-item');
        items.forEach(function(el) {
            var haystack = (el.getAttribute('data-search') || el.getAttribute('data-icon') || '').toLowerCase();
            el.style.display = (q === '' || haystack.indexOf(q) !== -1) ? '' : 'none';
        });
    }

    document.querySelectorAll('[data-dismiss="icon-picker-modal"]').forEach(function(btn) {
        btn.addEventListener('click', closePicker);
    });
    var backdrop = modal.querySelector('.absolute.inset-0');
    if (backdrop) backdrop.addEventListener('click', closePicker);

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.icon-picker-btn');
        if (btn) {
            e.preventDefault();
            var target = btn.getAttribute('data-target');
            if (target) openPicker(target);
        }
    });

    list.addEventListener('click', function(e) {
        var item = e.target.closest('.icon-picker-item');
        if (!item || !currentTarget) return;
        var icon = item.getAttribute('data-icon');
        if (icon) {
            currentTarget.value = icon;
            currentTarget.dispatchEvent(new Event('input', { bubbles: true }));
            closePicker();
        }
    });

    if (search) search.addEventListener('input', filterIcons);
    if (search) search.addEventListener('keydown', function(e) { if (e.key === 'Escape') closePicker(); });
});
</script>
