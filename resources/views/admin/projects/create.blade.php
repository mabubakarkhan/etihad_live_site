<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Create Project | Etihad Admin</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
        <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        @include('admin.projects._tom_select_dark')
        @include('admin.projects._vertical_tabs_style')
        @include('admin.partials.icon_picker')
    </head>
    <body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors">
        <div class="min-h-screen flex">
            @include('admin.partials.sidebar')
            <main class="flex-1 bg-gradient-to-br from-slate-100 via-slate-50 to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 overflow-auto transition-colors">
                <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800/70 flex items-center justify-between sticky top-0 bg-slate-100/95 dark:bg-slate-950/95 z-10 flex-wrap gap-3">
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">Create project</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Add a new real estate project. Fill sections as needed.</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Back to projects</a>
                        <a href="{{ route('admin.project_types.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Project types</a>
                    </div>
                </header>
                <section class="px-6 md:px-8 py-6 md:py-8">
                    <div id="form-errors-top" class="mb-4 hidden rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200" role="alert">
                        <p class="font-medium mb-1">Please fix the following:</p>
                        <ul id="form-errors-list" class="list-disc list-inside space-y-0.5"></ul>
                    </div>
                    @if ($errors->any())
                        @php
                            $projectSectionMap = [
                                'title' => ['tab' => 'tab-basics', 'name' => 'Basics'],
                                'slug' => ['tab' => 'tab-basics', 'name' => 'Basics'],
                                'status' => ['tab' => 'tab-status', 'name' => 'Status'],
                                'price' => ['tab' => 'tab-basics', 'name' => 'Basics'],
                                'description' => ['tab' => 'tab-basics', 'name' => 'Basics'],
                                'project_type_ids' => ['tab' => 'tab-basics', 'name' => 'Basics'],
                                'state' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'city' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'short_address' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'full_address' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'google_map' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'latitude' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'longitude' => ['tab' => 'tab-address', 'name' => 'Address'],
                                'logo' => ['tab' => 'tab-media', 'name' => 'Project media'],
                                'featured_image' => ['tab' => 'tab-media', 'name' => 'Project media'],
                                'homepage_listing_image' => ['tab' => 'tab-media', 'name' => 'Project media'],
                                'featured_youtube_url' => ['tab' => 'tab-featured-video', 'name' => 'Featured video'],
                                'featured_video_title' => ['tab' => 'tab-featured-video', 'name' => 'Featured video'],
                                'featured_video_description' => ['tab' => 'tab-featured-video', 'name' => 'Featured video'],
                                'about_developers' => ['tab' => 'tab-about', 'name' => 'About developers'],
                                'project_file_pdf' => ['tab' => 'tab-pdf', 'name' => 'Project file (PDF)'],
                                'noc_planning_content' => ['tab' => 'tab-noc', 'name' => 'NOC & planning'],
                                'noc_planning_image' => ['tab' => 'tab-noc', 'name' => 'NOC & planning'],
                                'future_note_title' => ['tab' => 'tab-future-note', 'name' => 'Future note'],
                                'future_note_content' => ['tab' => 'tab-future-note', 'name' => 'Future note'],
                                'extra_section_title' => ['tab' => 'tab-extra', 'name' => 'Extra section'],
                                'extra_section_content' => ['tab' => 'tab-extra', 'name' => 'Extra section'],
                                'price_plan_section_title' => ['tab' => 'tab-price-plan', 'name' => 'Price plan'],
                                'meta_title' => ['tab' => 'tab-seo', 'name' => 'SEO'],
                                'meta_description' => ['tab' => 'tab-seo', 'name' => 'SEO'],
                                'meta_keywords' => ['tab' => 'tab-seo', 'name' => 'SEO'],
                                'canonical_url' => ['tab' => 'tab-seo', 'name' => 'SEO'],
                            ];
                        @endphp
                        <div class="mb-4 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-800 dark:text-rose-200" role="alert">
                            <p class="font-medium mb-1">Please fix the following:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->getBag('default')->getMessages() as $field => $messages)
                                    @php $section = $projectSectionMap[$field] ?? ['tab' => 'tab-basics', 'name' => 'Basics']; @endphp
                                    @foreach($messages as $msg)
                                        <li>{{ $msg }} <span class="text-rose-700 dark:text-rose-300 font-medium">(Section: {{ $section['name'] }})</span>
                                            <button type="button" class="go-to-tab ml-1 text-xs underline hover:no-underline font-semibold" data-tab="{{ $section['tab'] }}">Go to: {{ $section['name'] }}</button>
                                        </li>
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data" id="project-form">
                        @csrf
                        <div class="mb-6 flex flex-wrap items-center gap-3">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">Create project</button>
                            <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 dark:border-slate-600 px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Cancel</a>
                        </div>
                        @include('admin.projects._form_sections', ['project' => null, 'projectTypes' => $projectTypes, 'states' => $states])
                    </form>
                </section>
            </main>
        </div>
        @include('admin.projects._form_scripts')
    </body>
</html>
