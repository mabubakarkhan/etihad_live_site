<aside class="w-72 bg-slate-200 dark:bg-slate-900 border-r border-slate-300 dark:border-slate-800 flex flex-col transition-colors">
    <div class="px-6 py-5 border-b border-slate-300 dark:border-slate-800 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="h-9 w-9 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center">
                <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-lg">E</span>
            </div>
            <div>
                <p class="text-sm font-semibold tracking-wide uppercase text-slate-800 dark:text-slate-200">
                    Etihad
                </p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    Admin Control Center
                </p>
            </div>
        </div>
        <a href="{{ route('admin.notifications.index') }}" class="relative inline-flex items-center justify-center w-9 h-9 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-600 dark:text-amber-400 hover:bg-amber-500/30 transition" title="Notifications" aria-label="Notifications">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a2 2 0 0 1 2 2v.29A7 7 0 0 1 19 11v5.29l1.5 1.5a1 1 0 0 1-1.42 1.41L18 17.59V18a2 2 0 0 1-4 0v-.41l-1.09-1.09A1 1 0 0 1 13 16v-5a1 1 0 0 0-2 0v5a1 1 0 0 1-.59.91L9.41 17.59 8 19a1 1 0 1 1-1.41-1.42L8 16.59V11a7 7 0 0 1 5-6.71V4a2 2 0 0 1 2-2zm0 18a2 2 0 0 0 1.73-1H10.27A2 2 0 0 0 12 20z"/></svg>
            @if(($adminUnreadNotificationCount ?? 0) > 0)
            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full bg-rose-500 text-white text-[10px] font-bold px-1">{{ $adminUnreadNotificationCount > 99 ? '99+' : $adminUnreadNotificationCount }}</span>
            @endif
        </a>
    </div>

    <nav class="flex-1 px-4 py-4 space-y-1 text-sm">
        <div class="text-[11px] font-semibold tracking-[0.18em] text-slate-500 uppercase px-2 mb-2">
            Overview
        </div>
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 shadow-sm shadow-emerald-500/20 border border-emerald-500/40' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
            <span class="h-7 w-7 rounded-lg bg-emerald-500/10 border border-emerald-500/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                &#x25CF;
            </span>
            <div class="flex flex-col">
                <span class="font-medium text-sm">Dashboard</span>
                <span class="text-[11px] text-slate-500 dark:text-slate-400">Key metrics & quick insights</span>
            </div>
        </a>

        <div class="mt-5 space-y-4">
            <div>
                <div class="text-[11px] font-semibold tracking-[0.18em] text-slate-500 uppercase px-2 mb-2">
                    Projects &amp; listings
                </div>
                <div class="space-y-1">
            <a href="{{ route('admin.projects.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.projects.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.projects.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">P</span>
                    <span class="text-sm">Projects</span>
                </span>
            </a>
            <a href="{{ route('admin.project_types.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.project_types.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.project_types.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">T</span>
                    <span class="text-sm">Project types</span>
                </span>
            </a>
            <a href="{{ route('admin.own-listings.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.own-listings.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.own-listings.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">O</span>
                    <span class="text-sm">Own listings</span>
                </span>
            </a>
            <a href="{{ route('admin.dealer-listings.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dealer-listings.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.dealer-listings.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">L</span>
                    <span class="text-sm">Dealer listings</span>
                </span>
            </a>
            <a href="{{ route('admin.dealers.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dealers.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.dealers.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">D</span>
                    <span class="text-sm">Dealers</span>
                </span>
            </a>
            <a href="{{ route('admin.requests.projects') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.requests.projects') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.requests.projects') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">Q</span>
                    <span class="text-sm">Project requests</span>
                </span>
            </a>
            <a href="{{ route('admin.requests.properties') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.requests.properties') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.requests.properties') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">Q</span>
                    <span class="text-sm">Property requests</span>
                </span>
            </a>
                </div>
            </div>

            <div>
                <div class="text-[11px] font-semibold tracking-[0.18em] text-slate-500 uppercase px-2 mb-2">
                    Homepage &amp; marketing
                </div>
                <div class="space-y-1">
            <a href="{{ route('admin.partners.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.partners.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-[10px] font-semibold {{ request()->routeIs('admin.partners.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">N</span>
                    <span class="text-sm">Partners</span>
                </span>
            </a>
            <a href="{{ route('admin.testimonials.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.testimonials.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-[10px] font-semibold {{ request()->routeIs('admin.testimonials.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">★</span>
                    <span class="text-sm">Testimonials</span>
                </span>
            </a>
            <a href="{{ route('admin.portal-hero.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.portal-hero.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-[10px] font-semibold {{ request()->routeIs('admin.portal-hero.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">H</span>
                    <span class="text-sm">Portal hero</span>
                </span>
            </a>
            <a href="{{ route('admin.cms-pages.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.cms-pages.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.cms-pages.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">S</span>
                    <span class="text-sm">CMS Pages</span>
                </span>
                @if(request()->routeIs('admin.cms-pages.*'))
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400">active</span>
                @endif
            </a>
                </div>
            </div>

            <div>
                <div class="text-[11px] font-semibold tracking-[0.18em] text-slate-500 uppercase px-2 mb-2">
                    Careers
                </div>
                <div class="space-y-1">
            <a href="{{ route('admin.careers.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.careers.index') || request()->routeIs('admin.careers.create') || request()->routeIs('admin.careers.edit') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.careers.index') || request()->routeIs('admin.careers.create') || request()->routeIs('admin.careers.edit') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">J</span>
                    <span class="text-sm">Careers</span>
                </span>
                @if(request()->routeIs('admin.careers.index') || request()->routeIs('admin.careers.create') || request()->routeIs('admin.careers.edit'))
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400">jobs</span>
                @endif
            </a>
            <a href="{{ route('admin.job-applications.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.job-applications.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.job-applications.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">CV</span>
                    <span class="text-sm">Job Applications</span>
                </span>
                @if(request()->routeIs('admin.job-applications.*'))
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400">active</span>
                @endif
            </a>
                </div>
            </div>

            <div>
                <div class="text-[11px] font-semibold tracking-[0.18em] text-slate-500 uppercase px-2 mb-2">
                    Settings &amp; insights
                </div>
                <div class="space-y-1">
            <a href="{{ route('admin.contact-settings.edit') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.contact-settings.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.contact-settings.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">
                        C
                    </span>
                    <span class="text-sm">Contact settings</span>
                </span>
                @if(request()->routeIs('admin.contact-settings.*'))
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400">active</span>
                @endif
            </a>
            <a href="{{ route('admin.reports.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.reports.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.reports.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">
                        R
                    </span>
                    <span class="text-sm">Reports</span>
                </span>
                @if(request()->routeIs('admin.reports.*'))
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400">active</span>
                @endif
            </a>
            <a href="{{ route('admin.activity_logs.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.activity_logs.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.activity_logs.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">
                        A
                    </span>
                    <span class="text-sm">Activity</span>
                </span>
                @if(request()->routeIs('admin.activity_logs.*'))
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400">logs</span>
                @else
                    <span class="text-[11px] text-slate-500">logs</span>
                @endif
            </a>
                </div>
            </div>

            <div>
                <div class="text-[11px] font-semibold tracking-[0.18em] text-slate-500 uppercase px-2 mb-2">
                    Administration
                </div>
                <div class="space-y-1">
            <a href="{{ route('admin.users.index') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.users.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">
                        U
                    </span>
                    <span class="text-sm">Users</span>
                </span>
                @if(request()->routeIs('admin.users.*'))
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400">active</span>
                @else
                    <span class="text-[11px] text-slate-500">manage</span>
                @endif
            </a>
            <a href="{{ route('admin.profile.show') }}"
               class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.profile*') ? 'bg-slate-300 dark:bg-slate-800 text-slate-900 dark:text-slate-50 border border-emerald-500/40 shadow-sm shadow-emerald-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-300/80 dark:hover:bg-slate-800/60' }}">
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs {{ request()->routeIs('admin.profile*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">
                        ME
                    </span>
                    <span class="text-sm">My profile</span>
                </span>
                @if(request()->routeIs('admin.profile*'))
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400">active</span>
                @endif
            </a>
            <button type="button" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-500 cursor-default" disabled>
                <span class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-xs text-slate-500">
                        B
                    </span>
                    <span class="text-sm">Bookings</span>
                </span>
                <span class="text-[11px] text-slate-500">soon</span>
            </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="px-4 py-4 border-t border-slate-300 dark:border-slate-800 text-[11px] text-slate-500">
        <p class="mb-1">Signed in as</p>
        <p class="text-slate-700 dark:text-slate-300 font-medium">{{ $adminUser?->name ?? $adminUser?->username ?? 'Admin' }}</p>
    </div>
</aside>
