@extends('admin.layouts.app')

@section('title', 'Contact Messages')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
        <div class="px-4 py-3 border-b border-orange-200/70 dark:border-orange-800/40 bg-gradient-to-r from-orange-50 via-white to-orange-100/70 dark:from-orange-950/30 dark:via-slate-900/90 dark:to-orange-900/20">
            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Filters</h2>
        </div>
        <form method="get" class="p-4 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="space-y-1 sm:col-span-3">
                    <label for="search" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Search</label>
                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Name, email, phone, message..."
                        class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition"
                    >
                </div>
                <div class="space-y-1 sm:col-span-2 lg:col-span-2">
                    <label for="status" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Status</label>
                    <select id="status" name="status" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition">
                        <option value="">All</option>
                        <option value="new" {{ $status === 'new' ? 'selected' : '' }}>New</option>
                        <option value="seen" {{ $status === 'seen' ? 'selected' : '' }}>Seen</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-amber-400 px-4 py-2 text-sm font-medium text-slate-950 shadow shadow-orange-500/40 hover:from-orange-400 hover:to-amber-300 transition">
                    Apply filters
                </button>
                @if(!empty($search) || !empty($status))
                    <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors admin-datatable-wrapper">
        <table class="min-w-full text-sm admin-datatable">
            <thead class="bg-slate-100 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-xs uppercase text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Contact</th>
                    <th class="px-4 py-2 text-left">Message</th>
                    <th class="px-4 py-2 text-left">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse($messages as $m)
                <tr class="bg-white dark:bg-slate-900/50">
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium {{ $m->status === 'new' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200 border border-amber-300 dark:border-amber-700' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600' }}">
                            {{ strtoupper($m->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-slate-800 dark:text-slate-200 font-medium">
                        <a class="text-sky-600 dark:text-sky-400 hover:underline" href="{{ route('admin.contact-messages.show', $m) }}">{{ $m->name }}</a>
                    </td>
                    <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ $m->email ?: '-' }}<br><span class="text-xs">{{ $m->phone ?: '-' }}</span></td>
                    <td class="px-4 py-2 text-slate-600 dark:text-slate-400 max-w-[260px] truncate" title="{{ $m->message }}">{{ \Illuminate\Support\Str::limit($m->message, 80) }}</td>
                    <td class="px-4 py-2 text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ optional($m->created_at)->format('Y-m-d H:i') }}</td>
                </tr>
                @empty
                <tr data-empty><td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-500">No messages found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

