<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Contact settings | Etihad Admin</title>
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
                            Contact settings
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Site contact info, address, and social links. All fields are optional.
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
                    @if (session('status'))
                        <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-800 dark:text-emerald-200">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg shadow-slate-200/80 dark:shadow-slate-950/50 transition-colors max-w-3xl">
                        <form method="POST" action="{{ route('admin.contact-settings.update') }}" class="space-y-4 text-sm">
                            @csrf
                            @method('PUT')

                            @if ($errors->any())
                                <div class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-800 dark:text-rose-200">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <div class="space-y-1.5">
                                <label for="address" class="block text-slate-700 dark:text-slate-300">Address</label>
                                <textarea id="address" name="address" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" placeholder="Full address">{{ old('address', $contactSetting->address ?? '') }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="latitude" class="block text-slate-700 dark:text-slate-300">Latitude</label>
                                    <input id="latitude" name="latitude" type="text" value="{{ old('latitude', $contactSetting->latitude ?? '') }}" placeholder="e.g. 31.5204" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="longitude" class="block text-slate-700 dark:text-slate-300">Longitude</label>
                                    <input id="longitude" name="longitude" type="text" value="{{ old('longitude', $contactSetting->longitude ?? '') }}" placeholder="e.g. 74.3587" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label for="email" class="block text-slate-700 dark:text-slate-300">Email</label>
                                    <input id="email" name="email" type="text" value="{{ old('email', $contactSetting->email ?? '') }}" placeholder="info@example.com" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="phone" class="block text-slate-700 dark:text-slate-300">Phone</label>
                                    <input id="phone" name="phone" type="text" value="{{ old('phone', $contactSetting->phone ?? '') }}" placeholder="+92 42 123 4567" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label for="timings" class="block text-slate-700 dark:text-slate-300">Timings</label>
                                <input id="timings" name="timings" type="text" value="{{ old('timings', $contactSetting->timings ?? '') }}" placeholder="e.g. Mon – Sat: 10:00 AM – 7:00 PM" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                            </div>

                            <div class="space-y-1.5">
                                <label for="whatsapp" class="block text-slate-700 dark:text-slate-300">WhatsApp</label>
                                <input id="whatsapp" name="whatsapp" type="text" value="{{ old('whatsapp', $contactSetting->whatsapp ?? '') }}" placeholder="+92 300 1234567 or full URL" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                            </div>

                            <div class="pt-2 border-t border-slate-200 dark:border-slate-700">
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Social links</p>
                                <div class="space-y-3">
                                    <div class="space-y-1.5">
                                        <label for="facebook" class="block text-slate-700 dark:text-slate-300">Facebook</label>
                                        <input id="facebook" name="facebook" type="text" value="{{ old('facebook', $contactSetting->facebook ?? '') }}" placeholder="https://facebook.com/..." class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="instagram" class="block text-slate-700 dark:text-slate-300">Instagram</label>
                                        <input id="instagram" name="instagram" type="text" value="{{ old('instagram', $contactSetting->instagram ?? '') }}" placeholder="https://instagram.com/..." class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="linkedin" class="block text-slate-700 dark:text-slate-300">LinkedIn</label>
                                        <input id="linkedin" name="linkedin" type="text" value="{{ old('linkedin', $contactSetting->linkedin ?? '') }}" placeholder="https://linkedin.com/..." class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="youtube" class="block text-slate-700 dark:text-slate-300">YouTube</label>
                                        <input id="youtube" name="youtube" type="text" value="{{ old('youtube', $contactSetting->youtube ?? '') }}" placeholder="https://youtube.com/..." class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="twitter" class="block text-slate-700 dark:text-slate-300">Twitter (X)</label>
                                        <input id="twitter" name="twitter" type="text" value="{{ old('twitter', $contactSetting->twitter ?? '') }}" placeholder="https://x.com/... or https://twitter.com/..." class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label for="tiktok" class="block text-slate-700 dark:text-slate-300">TikTok</label>
                                        <input id="tiktok" name="tiktok" type="text" value="{{ old('tiktok', $contactSetting->tiktok ?? '') }}" placeholder="https://tiktok.com/@..." class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow shadow-emerald-500/40 hover:bg-emerald-400 transition">
                                    Save contact settings
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
