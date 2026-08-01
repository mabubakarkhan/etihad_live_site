<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminBlogTagController extends Controller
{
    public function index()
    {
        $tags = BlogTag::query()
            ->withCount('posts')
            ->orderBy('name')
            ->limit(5000)
            ->get();

        return view('admin.blog_tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.blog_tags.create', ['tag' => new BlogTag()]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateTag($request);
        $tag = BlogTag::create($validated);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'blog_tag_created', "Blog tag created: {$tag->name} (ID: {$tag->id}).");
        }

        return redirect()->route('admin.blog-tags.index')->with('status', 'Tag created.');
    }

    public function edit(BlogTag $blogTag)
    {
        return view('admin.blog_tags.edit', ['tag' => $blogTag]);
    }

    public function update(Request $request, BlogTag $blogTag)
    {
        $validated = $this->validateTag($request, $blogTag);
        $blogTag->update($validated);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'blog_tag_updated', "Blog tag updated: {$blogTag->name} (ID: {$blogTag->id}).");
        }

        return redirect()->route('admin.blog-tags.index')->with('status', 'Tag updated.');
    }

    public function destroy(BlogTag $blogTag)
    {
        $name = $blogTag->name;
        $id = $blogTag->id;
        $blogTag->posts()->detach();
        $blogTag->delete();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'blog_tag_deleted', "Blog tag deleted: {$name} (ID: {$id}).");
        }

        return redirect()->route('admin.blog-tags.index')->with('status', 'Tag deleted.');
    }

    private function validateTag(Request $request, ?BlogTag $tag = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blog_tags', 'slug')->ignore($tag?->id),
            ],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'meta_robots' => ['nullable', 'string', 'max:120'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'string', 'max:500'],
        ]);

        if (! empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        } else {
            $validated['slug'] = Str::slug($validated['name']);
        }

        return $validated;
    }
}
