<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PortalHeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortalHeroSlideController extends Controller
{
    public function index()
    {
        $slides = PortalHeroSlide::query()->orderBy('sort_order')->orderBy('id')->limit(500)->get();

        return view('admin.portal_hero.index', compact('slides'));
    }

    public function create()
    {
        return view('admin.portal_hero.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'max:8192'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['image'] = $request->file('image')->store('portal-hero', 'public');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active', true);

        $slide = PortalHeroSlide::create($validated);
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'portal_hero_slide_created', "Portal hero slide created (ID: {$slide->id}).");
        }

        return redirect()->route('admin.portal-hero.index')->with('status', 'Slide added.');
    }

    public function edit(PortalHeroSlide $portal_hero_slide)
    {
        $slide = $portal_hero_slide;

        return view('admin.portal_hero.edit', compact('slide'));
    }

    public function update(Request $request, PortalHeroSlide $portal_hero_slide)
    {
        $validated = $request->validate([
            'image' => ['nullable', 'image', 'max:8192'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($portal_hero_slide->image) {
                Storage::disk('public')->delete($portal_hero_slide->image);
            }
            $validated['image'] = $request->file('image')->store('portal-hero', 'public');
        } else {
            unset($validated['image']);
        }

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? $portal_hero_slide->sort_order);
        $validated['is_active'] = $request->boolean('is_active');

        $portal_hero_slide->update($validated);
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'portal_hero_slide_updated', "Portal hero slide updated (ID: {$portal_hero_slide->id}).");
        }

        return redirect()->route('admin.portal-hero.index')->with('status', 'Slide saved.');
    }

    public function destroy(PortalHeroSlide $portal_hero_slide)
    {
        $id = $portal_hero_slide->id;
        if ($portal_hero_slide->image) {
            Storage::disk('public')->delete($portal_hero_slide->image);
        }
        $portal_hero_slide->delete();
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'portal_hero_slide_deleted', "Portal hero slide deleted (ID: {$id}).");
        }

        return redirect()->route('admin.portal-hero.index')->with('status', 'Slide deleted.');
    }
}
