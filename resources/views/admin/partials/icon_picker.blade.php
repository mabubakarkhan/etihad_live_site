{{-- Global icon picker modal. Include once per page. Buttons with class "icon-picker-btn" and data-target="input_id" open this and set the input value on select. --}}
<div id="icon-picker-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/70 dark:bg-slate-950/80 backdrop-blur-sm" data-dismiss="icon-picker-modal"></div>
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg max-h-[85vh] flex flex-col rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-2xl overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-2">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Choose icon</h3>
            <button type="button" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700" data-dismiss="icon-picker-modal" aria-label="Close">&times;</button>
        </div>
        <div class="p-3 border-b border-slate-200 dark:border-slate-700">
            <input type="text" id="icon-picker-search" placeholder="Search icons…" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-950 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400" />
        </div>
        <div id="icon-picker-list" class="flex-1 overflow-y-auto p-3 grid grid-cols-4 sm:grid-cols-6 gap-2">
            @php
                $heroicons = [
                    'academic-cap','adjustments','annotation','archive','arrow-down','arrow-left','arrow-right','arrow-up',
                    'at-symbol','backspace','badge-check','ban','beaker','bell','book-open','bookmark','briefcase',
                    'cake','calculator','calendar','camera','chart-bar','chart-pie','chat','check','check-circle',
                    'chevron-down','chevron-left','chevron-right','chevron-up','clipboard','clock','cloud','code','cog',
                    'collection','credit-card','cube','currency-bangladeshi','currency-dollar','currency-euro','currency-rupee',
                    'cursor-click','database','desktop-computer','document','document-add','document-download','document-duplicate',
                    'document-remove','document-text','dots-horizontal','dots-vertical','download','duplicate','emoji-happy',
                    'emoji-sad','exclamation','exclamation-circle','external-link','eye','eye-off','fast-forward','film',
                    'filter','finger-print','fire','flag','folder','folder-open','gift','globe','hand','hashtag',
                    'heart','home','identification','inbox','inbox-in','key','library','light-bulb','lightning-bolt',
                    'link','location-marker','lock-closed','lock-open','login','logout','mail','mail-open','map',
                    'menu','menu-alt-1','menu-alt-2','menu-alt-3','menu-alt-4','microphone','minus','minus-circle',
                    'moon','motorcycle','music-note','office-building','paper-airplane','paper-clip','pause','pencil',
                    'pencil-alt','phone','phone-incoming','phone-outgoing','phone-missed-call','photograph','play','plus',
                    'plus-circle','presentation-chart-line','printer','puzzle','qrcode','question-mark-circle','receipt-refund',
                    'refresh','reply','rewind','rss','save','scale','search','selector','server','share',
                    'shield-check','shopping-bag','shopping-cart','sort-ascending','sort-descending','sparkles','speakerphone',
                    'star','status-online','stop','sun','support','switch-vertical','table','tag','template',
                    'terminal','thumb-down','thumb-up','ticket','translate','trash','trending-down','trending-up',
                    'truck','upload','user','user-add','user-circle','user-group','user-remove','users',
                    'variable','video-camera','view-boards','view-grid','view-list','volume-off','volume-up','wifi',
                    'x','x-circle','zoom-in','zoom-out',
                ];
            @endphp
            @foreach($heroicons as $name)
                <button type="button" class="icon-picker-item flex flex-col items-center justify-center p-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 hover:bg-emerald-500/10 hover:border-emerald-500/40 dark:hover:bg-emerald-500/10 transition text-slate-700 dark:text-slate-300" data-icon="{{ $name }}" title="{{ $name }}">
                    <span class="icon-picker-icon-wrap h-6 w-6 mb-1 flex items-center justify-center rounded bg-slate-200 dark:bg-slate-700 text-slate-500">
                        <img src="https://api.iconify.design/heroicons-outline/{{ $name }}.svg?height=24" alt="" class="h-5 w-5 opacity-90 icon-picker-img" loading="lazy" onerror="this.style.display='none';this.nextElementSibling&&(this.nextElementSibling.style.display='inline');" />
                        <span class="icon-picker-fallback text-xs" style="display:none">◆</span>
                    </span>
                    <span class="text-[10px] font-mono truncate w-full text-center">{{ $name }}</span>
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
            var name = (el.getAttribute('data-icon') || '').toLowerCase();
            el.style.display = (q === '' || name.indexOf(q) !== -1) ? '' : 'none';
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
