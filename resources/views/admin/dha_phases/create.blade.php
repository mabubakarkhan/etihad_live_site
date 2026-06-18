<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add DHA Phase | Etihad Admin</title>
    @include('admin.partials.theme-init')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen">
<div class="min-h-screen flex">
    @include('admin.partials.sidebar')
    <main class="flex-1">
        <header class="px-8 pt-6 pb-4 border-b border-slate-200 dark:border-slate-800">
            <h1 class="text-xl font-semibold">Add DHA Phase</h1>
            <p class="text-sm text-slate-500 mt-1"><a href="{{ route('admin.dha-phases.index') }}" class="text-sky-600 hover:underline">Back to phases</a></p>
        </header>
        <section class="p-8">
            <form id="dha-phase-form" method="POST" action="{{ route('admin.dha-phases.store') }}" data-upload-url="{{ route('admin.dha-phases.upload-media') }}" data-phase-id="">
                @csrf
                @include('admin.dha_phases._form', ['selectedTypeIds' => []])
                <div class="mt-6"><button type="submit" class="rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950">Create phase</button></div>
            </form>
        </section>
    </main>
</div>
@stack('scripts')
</body>
</html>
