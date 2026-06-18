@extends('admin.layouts.app')

@section('title', 'Contact Message')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
        <div class="px-4 py-3 border-b border-orange-200/70 dark:border-orange-800/40 bg-gradient-to-r from-orange-50 via-white to-orange-100/70 dark:from-orange-950/30 dark:via-slate-900/90 dark:to-orange-900/20">
            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Message Details</h2>
        </div>
        <div class="p-4 md:p-5 space-y-3 text-sm">
            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ $contactMessage->name }}</h3>
            <p class="text-slate-600 dark:text-slate-300"><strong>Email:</strong> {{ $contactMessage->email ?: '-' }}</p>
            <p class="text-slate-600 dark:text-slate-300"><strong>Phone:</strong> {{ $contactMessage->phone ?: '-' }}</p>
            <p class="text-slate-600 dark:text-slate-300"><strong>Date:</strong> {{ optional($contactMessage->created_at)->format('Y-m-d H:i') }}</p>
            <div class="pt-2">
                <p class="font-semibold text-slate-800 dark:text-slate-200 mb-2">Message</p>
                <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-3 whitespace-pre-line text-slate-700 dark:text-slate-200">{{ $contactMessage->message }}</div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 shadow-lg overflow-hidden transition-colors">
        <div class="px-4 py-3 border-b border-orange-200/70 dark:border-orange-800/40 bg-gradient-to-r from-orange-50 via-white to-orange-100/70 dark:from-orange-950/30 dark:via-slate-900/90 dark:to-orange-900/20">
            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Update Status</h2>
        </div>
        <form method="post" action="{{ route('admin.contact-messages.update-status', $contactMessage) }}" class="p-4 flex flex-wrap items-end gap-3">
            @csrf
            @method('PUT')
            <div class="space-y-1">
                <label for="status" class="block text-xs font-medium text-slate-500 dark:text-slate-400">Status</label>
                <select id="status" name="status" class="rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition">
                    <option value="new" {{ $contactMessage->status === 'new' ? 'selected' : '' }}>New</option>
                    <option value="seen" {{ $contactMessage->status === 'seen' ? 'selected' : '' }}>Seen</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-amber-400 px-4 py-2 text-sm font-medium text-slate-950 shadow shadow-orange-500/40 hover:from-orange-400 hover:to-amber-300 transition">Update Status</button>
            <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Back</a>
        </form>
    </div>
</div>
@endsection

