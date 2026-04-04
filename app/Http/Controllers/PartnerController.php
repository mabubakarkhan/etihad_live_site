<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::query()->orderBy('id')->limit(500)->get();

        return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:5120'],
            'phone' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['image'] = $request->file('image')->store('partners', 'public');
        $validated['phone'] = $validated['phone'] ?? null;
        $validated['address'] = $validated['address'] ?? null;

        $partner = Partner::create($validated);
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'partner_created', "Partner created: {$partner->title} (ID: {$partner->id}).");
        }

        return redirect()->route('admin.partners.index')->with('status', 'Partner created.');
    }

    public function edit(Partner $partner)
    {
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
            'phone' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($request->hasFile('image')) {
            if ($partner->image) {
                Storage::disk('public')->delete($partner->image);
            }
            $validated['image'] = $request->file('image')->store('partners', 'public');
        } else {
            unset($validated['image']);
        }

        $validated['phone'] = $validated['phone'] ?? null;
        $validated['address'] = $validated['address'] ?? null;

        $partner->update($validated);
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'partner_updated', "Partner updated: {$partner->title} (ID: {$partner->id}).");
        }

        return redirect()->route('admin.partners.index')->with('status', 'Partner updated.');
    }

    public function destroy(Partner $partner)
    {
        $title = $partner->title;
        $id = $partner->id;
        if ($partner->image) {
            Storage::disk('public')->delete($partner->image);
        }
        $partner->delete();
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'partner_deleted', "Partner deleted: {$title} (ID: {$id}).");
        }

        return redirect()->route('admin.partners.index')->with('status', 'Partner deleted.');
    }
}
