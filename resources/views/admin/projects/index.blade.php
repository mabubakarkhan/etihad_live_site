<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Projects | Etihad Admin</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')
            <main class="flex-1 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 transition-colors">
                <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Projects</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Real estate projects.</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.profile.show') }}" class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">My profile</a>
                        <a href="{{ route('admin.project_types.index') }}" class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Project types</a>
                        <a href="{{ route('admin.sort-order.index', ['tab' => 'projects']) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-violet-500/50 text-violet-700 dark:text-violet-300 hover:bg-violet-500/10 transition">Sort order</a>
                        <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-500 text-slate-950 hover:bg-emerald-400 transition shadow shadow-emerald-500/40">Add project</a>
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-flex">@csrf<button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Logout</button></form>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8 space-y-4">
                    @php use App\Support\ProjectEditSections; @endphp
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif
                    {{-- Filters --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 overflow-hidden transition-colors">
                        <form method="GET" action="{{ route('admin.projects.index') }}" class="p-4 flex flex-wrap items-end gap-4">
                            <div class="space-y-1">
                                <label for="filter-status" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Status</label>
                                <select id="filter-status" name="status" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                                    <option value="">All</option>
                                    <option value="active" {{ ($filterStatus ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="hold" {{ ($filterStatus ?? '') === 'hold' ? 'selected' : '' }}>Hold</option>
                                    <option value="inactive" {{ ($filterStatus ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="close" {{ ($filterStatus ?? '') === 'close' ? 'selected' : '' }}>Close</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label for="filter-project-type" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Project type</label>
                                <select id="filter-project-type" name="project_type" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 min-w-[140px]">
                                    <option value="">All types</option>
                                    @foreach($projectTypes ?? [] as $pt)
                                        <option value="{{ $pt->id }}" {{ (string)($filterProjectType ?? '') === (string)$pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-emerald-400 transition">Apply</button>
                            @if (!empty($filterStatus) || !empty($filterProjectType))
                                <a href="{{ route('admin.projects.index') }}" class="rounded-lg border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Clear</a>
                            @endif
                        </form>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 overflow-hidden transition-colors admin-datatable-wrapper">
                        <table class="min-w-full text-sm admin-datatable">
                            <thead class="bg-slate-100 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-xs uppercase text-slate-500 dark:text-slate-400">
                                <tr>
                                    <th class="px-4 py-2 text-left w-16">Image</th>
                                    <th class="px-4 py-2 text-left">Title</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Type</th>
                                    <th class="px-4 py-2 text-left">Price</th>
                                    <th class="px-4 py-2 text-left">City</th>
                                    <th class="px-4 py-2 text-left min-w-[200px]">Sections</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse ($projects as $project)
                                    @php
                                        $thumb = $project->logo ?? $project->featured_image ?? $project->homepage_listing_image;
                                    @endphp
                                    <tr class="bg-white dark:bg-slate-900/50">
                                        <td class="px-4 py-2">
                                            @if($thumb)
                                                <img src="{{ asset('storage/' . $thumb) }}" alt="" class="h-10 w-10 object-cover rounded border border-slate-200 dark:border-slate-700" />
                                            @else
                                                <span class="inline-flex h-10 w-10 items-center justify-center rounded bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-500 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-slate-900 dark:text-slate-100">{{ $project->title }}</td>
                                        <td class="px-4 py-2">
                                            @php $st = $project->status ?? 'active'; @endphp
                                            @php $q = array_filter(['status' => $st, 'project_type' => $filterProjectType ?? '']); @endphp
                                            @if($st === 'active')
                                                <a href="{{ route('admin.projects.index', $q) }}" class="inline-flex rounded-full bg-emerald-500/15 px-2 py-0.5 text-[11px] font-medium text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 hover:opacity-90">Active</a>
                                            @elseif($st === 'hold')
                                                <a href="{{ route('admin.projects.index', $q) }}" class="inline-flex rounded-full bg-amber-500/15 px-2 py-0.5 text-[11px] font-medium text-amber-700 dark:text-amber-300 border border-amber-500/30 hover:opacity-90">Hold</a>
                                            @elseif($st === 'inactive')
                                                <a href="{{ route('admin.projects.index', $q) }}" class="inline-flex rounded-full bg-slate-400/20 px-2 py-0.5 text-[11px] font-medium text-slate-600 dark:text-slate-400 border border-slate-400/30 hover:opacity-90">Inactive</a>
                                            @else
                                                <a href="{{ route('admin.projects.index', $q) }}" class="inline-flex rounded-full bg-rose-500/15 px-2 py-0.5 text-[11px] font-medium text-rose-700 dark:text-rose-300 border border-rose-500/30 hover:opacity-90">Close</a>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-slate-500 dark:text-slate-400">
                                            @if($project->projectTypes->isNotEmpty())
                                                @foreach($project->projectTypes as $pt)
                                                    @php $typeQ = array_filter(['project_type' => $pt->id, 'status' => $filterStatus ?? '']); @endphp
                                                    <a href="{{ route('admin.projects.index', $typeQ) }}" class="inline-flex items-center rounded-full bg-slate-200 dark:bg-slate-800 px-2 py-0.5 text-[11px] text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 mr-1 hover:bg-slate-300 dark:hover:bg-slate-700 transition">{{ $pt->name }}</a>
                                                @endforeach
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-slate-700 dark:text-slate-300">{{ $project->price ?? '—' }}</td>
                                        <td class="px-4 py-2 text-slate-700 dark:text-slate-300">{{ $project->city ?? '—' }}</td>
                                        <td class="px-4 py-2">
                                            <div class="flex flex-wrap gap-1 max-w-md">
                                                @foreach(ProjectEditSections::all() as $slug => $meta)
                                                    <a href="{{ route('admin.projects.edit-section', [$project, $slug]) }}" class="text-[10px] leading-tight px-1.5 py-0.5 rounded border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">{{ $meta['label'] }}</a>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 text-left">
                                            <a href="{{ route('admin.projects.preview', $project) }}" target="_blank" rel="noopener noreferrer" class="text-[11px] px-2 py-1 rounded border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">View</a>
                                            <a href="{{ route('project.show', $project->slug) }}" target="_blank" rel="noopener noreferrer" class="text-[11px] px-2 py-1 rounded border border-sky-400 dark:border-sky-600 text-sky-700 dark:text-sky-300 hover:bg-sky-50 dark:hover:bg-sky-900/30 bg-sky-50/80 dark:bg-sky-900/20">Live</a>
                                            <a href="{{ route('admin.projects.edit', $project) }}" class="text-[11px] px-2 py-1 rounded border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Edit</a>
                                            <form method="POST" action="{{ route('admin.projects.duplicate', $project) }}" class="inline-block ml-1">@csrf<button type="submit" class="text-[11px] px-2 py-1 rounded border border-slate-400 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Duplicate</button></form>
                                            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="inline-block ml-1" onsubmit="return confirm('Delete this project?');">@csrf @method('DELETE')<button type="submit" class="text-[11px] px-2 py-1 rounded border border-rose-600/60 text-rose-600 dark:text-rose-300 hover:bg-rose-600/10">Delete</button></form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-empty><td colspan="8" class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-500">No projects yet. <a href="{{ route('admin.projects.create') }}" class="text-emerald-600 dark:text-emerald-400">Create one</a>.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>

        @include('admin.partials.datatables')
    </body>
</html>
