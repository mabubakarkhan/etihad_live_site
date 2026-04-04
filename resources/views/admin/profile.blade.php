<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>My Profile | Etihad Admin</title>
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
                        <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">
                            My profile
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Manage your account details, password, and personal settings.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @include('admin.partials.theme-toggle')
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline-flex">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </header>

                <section class="px-6 md:px-8 py-6 md:py-8 space-y-6">
                    <div class="grid gap-6 lg:grid-cols-3">
                        <div class="lg:col-span-2 space-y-4">
                            @if (session('status'))
                                <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors">
                                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-3">
                                    Profile information
                                </h2>

                                <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4 text-sm">
                                    @csrf
                                    @method('PUT')

                                    @if ($errors->any() && !$errors->has('current_password'))
                                        <div class="mb-2 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-1.5">
                                            <label for="name" class="block text-slate-700 dark:text-slate-300">Name</label>
                                            <input
                                                id="name"
                                                name="name"
                                                type="text"
                                                value="{{ old('name', $admin->name) }}"
                                                required
                                                class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                                            />
                                        </div>

                                        <div class="space-y-1.5">
                                            <label for="username" class="block text-slate-700 dark:text-slate-300">Username</label>
                                            <input
                                                id="username"
                                                name="username"
                                                type="text"
                                                value="{{ old('username', $admin->username) }}"
                                                required
                                                class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                                            />
                                        </div>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label for="email" class="block text-slate-700 dark:text-slate-300">Email</label>
                                        <input
                                            id="email"
                                            name="email"
                                            type="email"
                                            value="{{ old('email', $admin->email) }}"
                                            required
                                            class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                                        />
                                    </div>

                                    @php
                                        $settings = $admin->settings ?? [];
                                    @endphp

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-1.5">
                                            <label for="timezone" class="block text-slate-700 dark:text-slate-300">Timezone</label>
                                            <input
                                                id="timezone"
                                                name="timezone"
                                                type="text"
                                                value="{{ old('timezone', $settings['timezone'] ?? '') }}"
                                                placeholder="e.g. Asia/Dubai"
                                                class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                                            />
                                        </div>

                                        <div class="space-y-1.5">
                                            <label for="language" class="block text-slate-700 dark:text-slate-300">Language</label>
                                            <input
                                                id="language"
                                                name="language"
                                                type="text"
                                                value="{{ old('language', $settings['language'] ?? 'en') }}"
                                                class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                                            />
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2 pt-1">
                                        <label class="inline-flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                                            <input
                                                type="checkbox"
                                                name="dark_mode"
                                                value="1"
                                                class="rounded border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-950 text-emerald-500"
                                                {{ old('dark_mode', $settings['dark_mode'] ?? true) ? 'checked' : '' }}
                                            />
                                            <span>Prefer dark mode for admin panel</span>
                                        </label>
                                        <label class="inline-flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                                            <input
                                                type="checkbox"
                                                name="email_notifications"
                                                value="1"
                                                class="rounded border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-950 text-emerald-500"
                                                {{ old('email_notifications', $settings['email_notifications'] ?? true) ? 'checked' : '' }}
                                            />
                                            <span>Receive important email notifications (future use)</span>
                                        </label>
                                    </div>

                                    <div class="pt-2">
                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition"
                                        >
                                            Save profile
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors">
                                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-3">
                                    Change password
                                </h2>

                                @if (session('password_status'))
                                    <div class="mb-3 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">
                                        {{ session('password_status') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-3 text-sm">
                                    @csrf
                                    @method('PUT')

                                    @error('current_password')
                                        <div class="mb-2 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <div class="space-y-1.5">
                                        <label for="current_password" class="block text-slate-700 dark:text-slate-300">Current password</label>
                                        <input
                                            id="current_password"
                                            name="current_password"
                                            type="password"
                                            required
                                            class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                                        />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label for="password" class="block text-slate-700 dark:text-slate-300">New password</label>
                                        <input
                                            id="password"
                                            name="password"
                                            type="password"
                                            required
                                            class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                                        />
                                    </div>

                                    <div class="space-y-1.5">
                                        <label for="password_confirmation" class="block text-slate-700 dark:text-slate-300">Confirm new password</label>
                                        <input
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            type="password"
                                            required
                                            class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                                        />
                                    </div>

                                    <button
                                        type="submit"
                                        class="mt-2 inline-flex items-center justify-center rounded-lg bg-slate-200 dark:bg-slate-800 px-4 py-2.5 text-sm font-semibold text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-600 hover:bg-slate-300 dark:hover:bg-slate-700 transition"
                                    >
                                        Update password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
    </html>

