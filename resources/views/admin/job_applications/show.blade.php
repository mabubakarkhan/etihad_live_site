<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Application: {{ $jobApplication->name }} | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">{{ $jobApplication->name }}</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Applied for {{ $jobApplication->career->title }} · {{ $jobApplication->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.job-applications.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Back to applications</a>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8 space-y-6">
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">{{ session('status') }}</div>
                    @endif
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-6 shadow-lg transition-colors max-w-2xl">
                        <dl class="grid gap-4 text-sm">
                            <div><dt class="font-semibold text-slate-500 dark:text-slate-400">Name</dt><dd class="mt-0.5 text-slate-900 dark:text-slate-100">{{ $jobApplication->name }}</dd></div>
                            <div><dt class="font-semibold text-slate-500 dark:text-slate-400">Mobile</dt><dd class="mt-0.5"><a href="tel:{{ e($jobApplication->mobile) }}" class="text-emerald-600 dark:text-emerald-400">{{ $jobApplication->mobile }}</a></dd></div>
                            @if($jobApplication->email)<div><dt class="font-semibold text-slate-500 dark:text-slate-400">Email</dt><dd class="mt-0.5"><a href="mailto:{{ e($jobApplication->email) }}" class="text-emerald-600 dark:text-emerald-400">{{ $jobApplication->email }}</a></dd></div>@endif
                            @if($jobApplication->address)<div><dt class="font-semibold text-slate-500 dark:text-slate-400">Address</dt><dd class="mt-0.5 text-slate-700 dark:text-slate-300">{{ $jobApplication->address }}</dd></div>@endif
                            @if($jobApplication->city)<div><dt class="font-semibold text-slate-500 dark:text-slate-400">City</dt><dd class="mt-0.5 text-slate-700 dark:text-slate-300">{{ $jobApplication->city }}</dd></div>@endif
                            @if($jobApplication->education)<div><dt class="font-semibold text-slate-500 dark:text-slate-400">Education</dt><dd class="mt-0.5 text-slate-700 dark:text-slate-300">{{ $jobApplication->education }}</dd></div>@endif
                            @if($jobApplication->comments)<div><dt class="font-semibold text-slate-500 dark:text-slate-400">Comments</dt><dd class="mt-0.5 text-slate-700 dark:text-slate-300 whitespace-pre-wrap">{{ $jobApplication->comments }}</dd></div>@endif
                            @if($jobApplication->cv_path)
                            <div><dt class="font-semibold text-slate-500 dark:text-slate-400">CV</dt><dd class="mt-0.5"><a href="{{ asset('storage/' . $jobApplication->cv_path) }}" target="_blank" rel="noopener" class="text-emerald-600 dark:text-emerald-400 underline">Download CV</a></dd></div>
                            @endif
                        </dl>
                        <form method="POST" action="{{ route('admin.job-applications.update-status', $jobApplication) }}" class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            @csrf
                            @method('PUT')
                            <label for="status" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Applicant status</label>
                            <div class="flex flex-wrap items-center gap-3">
                                <select name="status" id="status" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100">
                                    <option value="new" {{ $jobApplication->status === 'new' ? 'selected' : '' }}>New</option>
                                    <option value="seen" {{ $jobApplication->status === 'seen' ? 'selected' : '' }}>Seen</option>
                                    <option value="accept" {{ $jobApplication->status === 'accept' ? 'selected' : '' }}>Accept</option>
                                    <option value="considering" {{ $jobApplication->status === 'considering' ? 'selected' : '' }}>Considering</option>
                                    <option value="rejected" {{ $jobApplication->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-slate-950 hover:bg-emerald-400 transition">Update status</button>
                            </div>
                        </form>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
