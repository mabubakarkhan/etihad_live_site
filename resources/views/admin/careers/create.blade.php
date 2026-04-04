<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Add Job | Careers | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Add job</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Create a new job posting.</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.careers.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Back to careers</a>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8">
                    <div class="max-w-2xl rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-6 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors">
                        @if ($errors->any())
                            <div class="mb-4 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">{{ $errors->first() }}</div>
                        @endif
                        <form method="POST" action="{{ route('admin.careers.store') }}" class="space-y-4">
                            @csrf
                            <div class="space-y-1.5">
                                <label for="title" class="block text-sm text-slate-700 dark:text-slate-300">Job title *</label>
                                <input id="title" name="title" type="text" value="{{ old('title') }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="e.g. Sales Manager" />
                            </div>
                            <div class="space-y-1.5">
                                <label for="slug" class="block text-sm text-slate-700 dark:text-slate-300">Slug</label>
                                <input id="slug" name="slug" type="text" value="{{ old('slug') }}" placeholder="sales-manager" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" />
                                <p class="text-xs text-slate-500 dark:text-slate-400">Leave empty to auto-generate from title. Use lowercase letters, numbers, and hyphens only.</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="location" class="block text-sm text-slate-700 dark:text-slate-300">Location</label>
                                    <input id="location" name="location" type="text" value="{{ old('location') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="e.g. Lahore" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="department" class="block text-sm text-slate-700 dark:text-slate-300">Department</label>
                                    <input id="department" name="department" type="text" value="{{ old('department') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="e.g. Sales" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="education" class="block text-sm text-slate-700 dark:text-slate-300">Education</label>
                                    <input id="education" name="education" type="text" value="{{ old('education') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="e.g. Bachelor’s (Business/Marketing)" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="experience" class="block text-sm text-slate-700 dark:text-slate-300">Experience</label>
                                    <input id="experience" name="experience" type="text" value="{{ old('experience') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="e.g. 2–4 years" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="timings" class="block text-sm text-slate-700 dark:text-slate-300">Timings</label>
                                    <input id="timings" name="timings" type="text" value="{{ old('timings') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="e.g. 10:00 AM – 06:00 PM" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="joining_month" class="block text-sm text-slate-700 dark:text-slate-300">Joining month</label>
                                    <input id="joining_month" name="joining_month" type="text" value="{{ old('joining_month') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="e.g. April 2026 or Open" />
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Leave empty if not decided. You can also type “Open”.</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="employment_type" class="block text-sm text-slate-700 dark:text-slate-300">Employment type</label>
                                    <input id="employment_type" name="employment_type" type="text" value="{{ old('employment_type') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="e.g. Full-time" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="salary_range" class="block text-sm text-slate-700 dark:text-slate-300">Salary range</label>
                                    <input id="salary_range" name="salary_range" type="text" value="{{ old('salary_range') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="e.g. PKR 80k–120k" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="vacancies" class="block text-sm text-slate-700 dark:text-slate-300">Vacancies</label>
                                    <input id="vacancies" name="vacancies" type="number" value="{{ old('vacancies') }}" min="0" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="1" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="apply_before" class="block text-sm text-slate-700 dark:text-slate-300">Apply before</label>
                                    <input id="apply_before" name="apply_before" type="date" value="{{ old('apply_before') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="apply_email" class="block text-sm text-slate-700 dark:text-slate-300">Apply email</label>
                                    <input id="apply_email" name="apply_email" type="email" value="{{ old('apply_email', $contactEmail ?? '') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="hr@example.com" />
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Defaults to Contact Settings email if empty.</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="apply_url" class="block text-sm text-slate-700 dark:text-slate-300">Apply URL</label>
                                    <input id="apply_url" name="apply_url" type="text" value="{{ old('apply_url') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="Leave empty to auto-fill with /careers/job/{slug}" />
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Leave empty to auto-generate from job slug after save.</p>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label for="requirements" class="block text-sm text-slate-700 dark:text-slate-300">Requirements / description</label>
                                <textarea id="requirements" name="requirements" rows="8" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="List responsibilities, qualifications, and any other details...">{{ old('requirements') }}</textarea>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Full job description and requirements. Plain text or simple HTML.</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg transition-colors">
                                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-4">SEO (meta tags)</h2>
                                <div class="space-y-4">
                                    <div class="space-y-1.5">
                                        <label for="meta_title" class="block text-sm text-slate-700 dark:text-slate-300">Meta title</label>
                                        <input id="meta_title" name="meta_title" type="text" value="{{ old('meta_title') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="Optional" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="meta_description" class="block text-sm text-slate-700 dark:text-slate-300">Meta description</label>
                                        <textarea id="meta_description" name="meta_description" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="Optional">{{ old('meta_description') }}</textarea>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="meta_keywords" class="block text-sm text-slate-700 dark:text-slate-300">Meta keywords</label>
                                        <input id="meta_keywords" name="meta_keywords" type="text" value="{{ old('meta_keywords') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="Comma separated, optional" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="canonical_url" class="block text-sm text-slate-700 dark:text-slate-300">Canonical URL</label>
                                        <input id="canonical_url" name="canonical_url" type="text" value="{{ old('canonical_url') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="Optional" />
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="status" class="block text-sm text-slate-700 dark:text-slate-300">Status</label>
                                    <select id="status" name="status" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100">
                                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Active jobs are shown on the careers page.</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="sort_order" class="block text-sm text-slate-700 dark:text-slate-300">Sort order</label>
                                    <input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order') }}" min="0" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100" placeholder="0" />
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Higher numbers appear first. Leave empty for default.</p>
                                </div>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Create job</button>
                        </form>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
