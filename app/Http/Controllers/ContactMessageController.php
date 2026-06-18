<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:60'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        if (empty($validated['email']) && empty($validated['phone'])) {
            return response()->json(['success' => false, 'message' => 'Please provide either email or phone.'], 422);
        }

        ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'],
            'status' => ContactMessage::STATUS_NEW,
        ]);

        return response()->json(['success' => true, 'message' => 'Thanks! Your message has been sent successfully.']);
    }

    public function index(Request $request): View
    {
        $q = ContactMessage::query();
        $status = (string) $request->input('status', '');
        if (in_array($status, [ContactMessage::STATUS_NEW, ContactMessage::STATUS_SEEN], true)) {
            $q->where('status', $status);
        }
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $term = '%' . $search . '%';
            $q->where(function ($x) use ($term) {
                $x->where('name', 'like', $term)->orWhere('email', 'like', $term)->orWhere('phone', 'like', $term)->orWhere('message', 'like', $term);
            });
        }
        $messages = $q->orderByRaw("CASE WHEN status='new' THEN 0 ELSE 1 END ASC")->orderByDesc('created_at')->limit(2000)->get();
        return view('admin.contact_messages.index', compact('messages', 'status', 'search'));
    }

    public function show(ContactMessage $contactMessage): View
    {
        if ($contactMessage->status === ContactMessage::STATUS_NEW) {
            $contactMessage->update(['status' => ContactMessage::STATUS_SEEN, 'seen_at' => now()]);
        }
        return view('admin.contact_messages.show', compact('contactMessage'));
    }

    public function updateStatus(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:new,seen']]);
        $contactMessage->update(['status' => $data['status'], 'seen_at' => $data['status'] === 'seen' ? now() : null]);
        return redirect()->route('admin.contact-messages.show', $contactMessage)->with('status', 'Message status updated.');
    }
}

