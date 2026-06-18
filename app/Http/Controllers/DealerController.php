<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Dealer;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DealerController extends Controller
{
    public function index()
    {
        $query = Dealer::withCount('properties')->orderBy('name');
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }
        if (request()->filled('show_homepage')) {
            $showHomepage = request('show_homepage');
            if ($showHomepage === 'yes') {
                $query->where('show_homepage', true);
            } elseif ($showHomepage === 'no') {
                $query->where(function ($q) {
                    $q->whereNull('show_homepage')->orWhere('show_homepage', false);
                });
            }
        }
        if (request()->filled('show_homepage_ad')) {
            $showHomepageAd = request('show_homepage_ad');
            if ($showHomepageAd === 'yes') {
                $query->where('show_homepage_ad', true);
            } elseif ($showHomepageAd === 'no') {
                $query->where(function ($q) {
                    $q->whereNull('show_homepage_ad')->orWhere('show_homepage_ad', false);
                });
            }
        }
        $dealers = $query->limit(2000)->get();
        $filterStatus = request('status');
        $filterShowHomepage = request('show_homepage');
        $filterShowHomepageAd = request('show_homepage_ad');
        return view('admin.dealers.index', compact('dealers', 'filterStatus', 'filterShowHomepage', 'filterShowHomepageAd'));
    }

    public function create()
    {
        $states = State::with('cities')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.dealers.create', compact('states'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:dealers,slug'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'show_homepage' => ['nullable', 'boolean'],
            'show_homepage_ad' => ['nullable', 'boolean'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'info_detail' => ['nullable', 'string', 'max:2000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
        ]);
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['show_homepage'] = $request->boolean('show_homepage');
        $validated['show_homepage_ad'] = $request->boolean('show_homepage_ad');
        if (isset($validated['slug']) && trim((string) $validated['slug']) !== '') {
            $validated['slug'] = \Illuminate\Support\Str::slug(trim($validated['slug']));
        } else {
            unset($validated['slug']);
        }

        if ($request->hasFile('profile_pic')) {
            $validated['profile_pic'] = $request->file('profile_pic')->store('dealers', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('dealers/banners', 'public');
        }

        $dealer = Dealer::create($validated);
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'dealer_created', "Dealer created: {$dealer->name} (ID: {$dealer->id}).");
        }
        return redirect()->route('admin.dealers.index')->with('status', 'Dealer created.');
    }

    public function edit(Dealer $dealer)
    {
        $dealer->loadCount('properties');
        $states = State::with('cities')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.dealers.edit', compact('dealer', 'states'));
    }

    public function update(Request $request, Dealer $dealer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:dealers,slug,' . $dealer->id],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'show_homepage' => ['nullable', 'boolean'],
            'show_homepage_ad' => ['nullable', 'boolean'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'info_detail' => ['nullable', 'string', 'max:2000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
        ]);
        if (! empty($validated['slug'] ?? '')) {
            $validated['slug'] = \Illuminate\Support\Str::slug(trim($validated['slug']));
        } else {
            $validated['slug'] = \Illuminate\Support\Str::slug($dealer->name);
        }
        $validated['show_homepage'] = $request->boolean('show_homepage');
        $validated['show_homepage_ad'] = $request->boolean('show_homepage_ad');

        if ($request->boolean('remove_profile_pic') && $dealer->profile_pic) {
            Storage::disk('public')->delete($dealer->profile_pic);
            $validated['profile_pic'] = null;
        } elseif ($request->hasFile('profile_pic')) {
            if ($dealer->profile_pic) {
                Storage::disk('public')->delete($dealer->profile_pic);
            }
            $validated['profile_pic'] = $request->file('profile_pic')->store('dealers', 'public');
        }
        if ($request->boolean('remove_banner_image') && $dealer->banner_image) {
            Storage::disk('public')->delete($dealer->banner_image);
            $validated['banner_image'] = null;
        }
        if ($request->hasFile('banner_image')) {
            if ($dealer->banner_image) {
                Storage::disk('public')->delete($dealer->banner_image);
            }
            $validated['banner_image'] = $request->file('banner_image')->store('dealers/banners', 'public');
        }

        $dealer->update($validated);
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'dealer_updated', "Dealer updated: {$dealer->name} (ID: {$dealer->id}).");
        }
        return redirect()->route('admin.dealers.index')->with('status', 'Dealer updated.');
    }

    public function destroy(Dealer $dealer)
    {
        $name = $dealer->name;
        $id = $dealer->id;
        if ($dealer->profile_pic) {
            Storage::disk('public')->delete($dealer->profile_pic);
        }
        if ($dealer->banner_image) {
            Storage::disk('public')->delete($dealer->banner_image);
        }
        $dealer->properties()->update(['dealer_id' => 0]);
        $dealer->delete();
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'dealer_deleted', "Dealer deleted: {$name} (ID: {$id}).");
        }
        return redirect()->route('admin.dealers.index')->with('status', 'Dealer deleted.');
    }
}
