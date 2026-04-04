<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Career;
use App\Models\ContactSetting;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function index()
    {
        $query = Career::orderByDesc('sort_order')->orderByDesc('id');
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }
        $careers = $query->limit(2000)->get();
        $filterStatus = request('status');
        return view('admin.careers.index', compact('careers', 'filterStatus'));
    }

    public function create()
    {
        $contactEmail = ContactSetting::instance()->email;
        $defaultApplyUrl = null;
        return view('admin.careers.create', compact('contactEmail', 'defaultApplyUrl'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:careers,slug'],
            'location' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:120'],
            'education' => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:255'],
            'timings' => ['nullable', 'string', 'max:255'],
            'joining_month' => ['nullable', 'string', 'max:60'],
            'employment_type' => ['nullable', 'string', 'max:80'],
            'salary_range' => ['nullable', 'string', 'max:120'],
            'vacancies' => ['nullable', 'integer', 'min:0'],
            'apply_before' => ['nullable', 'date'],
            'apply_email' => ['nullable', 'email', 'max:255'],
            'apply_url' => ['nullable', 'string', 'max:500'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'requirements' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:active,draft,closed'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $validated['status'] = $validated['status'] ?? 'active';
        if (isset($validated['slug']) && trim((string) $validated['slug']) !== '') {
            $validated['slug'] = \Illuminate\Support\Str::slug(trim($validated['slug']));
        } else {
            unset($validated['slug']);
        }

        $career = Career::create($validated);
        if (empty($career->apply_email)) {
            $career->apply_email = ContactSetting::instance()->email;
        }
        if (empty($career->apply_url) && $career->slug) {
            $career->apply_url = route('careers.job', $career->slug);
        }
        if ($career->isDirty(['apply_email', 'apply_url'])) {
            $career->save();
        }
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'career_created', "Career created: {$career->title} (ID: {$career->id}).");
        }
        return redirect()->route('admin.careers.index')->with('status', 'Job posting created.');
    }

    public function edit(Career $career)
    {
        $contactEmail = ContactSetting::instance()->email;
        $defaultApplyUrl = $career->slug ? route('careers.job', $career->slug) : null;
        return view('admin.careers.edit', compact('career', 'contactEmail', 'defaultApplyUrl'));
    }

    public function update(Request $request, Career $career)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:careers,slug,' . $career->id],
            'location' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:120'],
            'education' => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:255'],
            'timings' => ['nullable', 'string', 'max:255'],
            'joining_month' => ['nullable', 'string', 'max:60'],
            'employment_type' => ['nullable', 'string', 'max:80'],
            'salary_range' => ['nullable', 'string', 'max:120'],
            'vacancies' => ['nullable', 'integer', 'min:0'],
            'apply_before' => ['nullable', 'date'],
            'apply_email' => ['nullable', 'email', 'max:255'],
            'apply_url' => ['nullable', 'string', 'max:500'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'requirements' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:active,draft,closed'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        if (!empty($validated['slug'] ?? '')) {
            $validated['slug'] = \Illuminate\Support\Str::slug(trim($validated['slug']));
        } else {
            $validated['slug'] = \Illuminate\Support\Str::slug($career->title);
        }
        if (empty($validated['apply_email'] ?? null)) {
            $validated['apply_email'] = ContactSetting::instance()->email;
        }
        if (empty($validated['apply_url'] ?? null) && !empty($validated['slug'])) {
            $validated['apply_url'] = route('careers.job', $validated['slug']);
        }

        $career->update($validated);
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'career_updated', "Career updated: {$career->title} (ID: {$career->id}).");
        }
        return redirect()->route('admin.careers.index')->with('status', 'Job posting updated.');
    }

    public function destroy(Career $career)
    {
        $title = $career->title;
        $id = $career->id;
        $career->delete();
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'career_deleted', "Career deleted: {$title} (ID: {$id}).");
        }
        return redirect()->route('admin.careers.index')->with('status', 'Job posting deleted.');
    }
}
