<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Admin Login | Etihad</title>
        @include('admin.partials.theme-init')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' }</script>
    </head>
    <body class="min-h-screen bg-slate-100 dark:bg-slate-950 flex flex-col items-center px-4 py-8 transition-colors">
        <header class="w-full max-w-md flex justify-end mb-4">
            @include('admin.partials.theme-toggle')
        </header>
        <div class="w-full max-w-md flex-1 flex flex-col justify-center">
            <div class="mb-6 text-center">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl border border-emerald-500/40 bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 text-xl font-semibold">
                    E
                </div>
                <h1 class="text-xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">
                    Etihad Admin
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Sign in with your admin credentials to continue.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-6 shadow-xl shadow-slate-200/60 dark:shadow-slate-950/60 transition-colors">
                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5 text-sm">
                        <label for="username" class="block text-slate-700 dark:text-slate-300">
                            Username
                        </label>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            placeholder="admin"
                        />
                    </div>

                    <div class="space-y-1.5 text-sm">
                        <label for="password" class="block text-slate-700 dark:text-slate-300">
                            Password
                        </label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            placeholder="••••••••"
                        />
                    </div>

                    <button
                        type="submit"
                        class="mt-2 inline-flex w-full items-center justify-center rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition"
                    >
                        Sign in
                    </button>
                </form>

                <p class="mt-4 text-[11px] text-center text-slate-500 dark:text-slate-500">
                    Hint: username <span class="font-medium text-slate-700 dark:text-slate-300">admin</span>,
                    password <span class="font-medium text-slate-700 dark:text-slate-300">chor</span> (can be changed later).
                </p>
            </div>
        </div>
    </body>
</html>

