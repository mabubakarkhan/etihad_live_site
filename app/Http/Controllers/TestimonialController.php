<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::query()->orderByDesc('id')->limit(500)->get();

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:5120'],
            'comment' => ['required', 'string', 'max:10000'],
            'city' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['image'] = $request->file('image')->store('testimonials', 'public');
        $validated['city'] = $validated['city'] ?? null;

        $testimonial = Testimonial::create($validated);
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'testimonial_created', "Testimonial created: {$testimonial->name} (ID: {$testimonial->id}).");
        }

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial created.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
            'comment' => ['required', 'string', 'max:10000'],
            'city' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('image')) {
            if ($testimonial->image) {
                Storage::disk('public')->delete($testimonial->image);
            }
            $validated['image'] = $request->file('image')->store('testimonials', 'public');
        } else {
            unset($validated['image']);
        }

        $validated['city'] = $validated['city'] ?? null;

        $testimonial->update($validated);
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'testimonial_updated', "Testimonial updated: {$testimonial->name} (ID: {$testimonial->id}).");
        }

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $name = $testimonial->name;
        $id = $testimonial->id;
        if ($testimonial->image) {
            Storage::disk('public')->delete($testimonial->image);
        }
        $testimonial->delete();
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'testimonial_deleted', "Testimonial deleted: {$name} (ID: {$id}).");
        }

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial deleted.');
    }
}
