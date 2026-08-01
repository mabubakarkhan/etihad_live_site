<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminBlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::query()
            ->withCount('posts')
            ->orderBy('name')
            ->limit(1000)
            ->get();

        return view('admin.blog_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.blog_categories.create', ['category' => new BlogCategory()]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);
        $category = BlogCategory::create($validated);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'blog_category_created', "Blog category created: {$category->name} (ID: {$category->id}).");
        }

        return redirect()->route('admin.blog-categories.index')->with('status', 'Category created.');
    }

    public function edit(BlogCategory $blogCategory)
    {
        return view('admin.blog_categories.edit', ['category' => $blogCategory]);
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $validated = $this->validateCategory($request, $blogCategory);
        $blogCategory->update($validated);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'blog_category_updated', "Blog category updated: {$blogCategory->name} (ID: {$blogCategory->id}).");
        }

        return redirect()->route('admin.blog-categories.index')->with('status', 'Category updated.');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        $name = $blogCategory->name;
        $id = $blogCategory->id;
        $blogCategory->posts()->detach();
        $blogCategory->delete();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'blog_category_deleted', "Blog category deleted: {$name} (ID: {$id}).");
        }

        return redirect()->route('admin.blog-categories.index')->with('status', 'Category deleted.');
    }

    private function validateCategory(Request $request, ?BlogCategory $category = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blog_categories', 'slug')->ignore($category?->id),
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
