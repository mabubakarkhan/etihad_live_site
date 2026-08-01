<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminBlogPostController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::query()
            ->with(['categories:id,name', 'author:id,name'])
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $posts = $query->limit(3000)->get();
        $filterStatus = $request->input('status');

        return view('admin.blog_posts.index', compact('posts', 'filterStatus'));
    }

    public function create()
    {
        $categories = BlogCategory::orderBy('name')->get(['id', 'name']);
        $tags = BlogTag::orderBy('name')->get(['id', 'name']);
        $post = new BlogPost([
            'status' => BlogPost::STATUS_DRAFT,
            'published_at' => now(),
        ]);

        return view('admin.blog_posts.create', compact('post', 'categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePost($request);
        $validated = $this->applySlug($validated);
        $validated['author_id'] = optional(admin_user())->id;
        $validated = $this->applyFeaturedMedia($request, $validated, null);
        $validated = $this->applySeoMedia($request, $validated);

        $post = BlogPost::create($validated);
        $this->syncTaxonomy($request, $post);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'blog_post_created', "Blog post created: {$post->title} (ID: {$post->id}).");
        }

        return redirect()->route('admin.blog-posts.index')->with('status', 'Blog post created.');
    }

    public function edit(BlogPost $blogPost)
    {
        $blogPost->load(['categories:id', 'tags:id']);
        $categories = BlogCategory::orderBy('name')->get(['id', 'name']);
        $tags = BlogTag::orderBy('name')->get(['id', 'name']);

        return view('admin.blog_posts.edit', [
            'post' => $blogPost,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $validated = $this->validatePost($request, $blogPost);
        $validated = $this->applySlug($validated, $blogPost);
        $validated = $this->applyFeaturedMedia($request, $validated, $blogPost);
        $validated = $this->applySeoMedia($request, $validated);

        $blogPost->update($validated);
        $this->syncTaxonomy($request, $blogPost);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'blog_post_updated', "Blog post updated: {$blogPost->title} (ID: {$blogPost->id}).");
        }

        return redirect()->route('admin.blog-posts.index')->with('status', 'Blog post updated.');
    }

    public function destroy(BlogPost $blogPost)
    {
        $title = $blogPost->title;
        $id = $blogPost->id;

        if ($blogPost->featured_image) {
            public_storage_delete($blogPost->featured_image);
        }

        $blogPost->categories()->detach();
        $blogPost->tags()->detach();
        $blogPost->delete();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'blog_post_deleted', "Blog post deleted: {$title} (ID: {$id}).");
        }

        return redirect()->route('admin.blog-posts.index')->with('status', 'Blog post deleted.');
    }

    private function validatePost(Request $request, ?BlogPost $post = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blog_posts', 'slug')->ignore($post?->id),
            ],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:published,draft'],
            'published_at' => ['nullable', 'date'],
            'featured_image' => ['nullable', 'image', 'max:8192'],
            'featured_image_source' => ['nullable', 'string', 'max:500'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:blog_categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:blog_tags,id'],
            'new_tags' => ['nullable', 'string', 'max:1000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'focus_keyphrase' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'meta_robots' => ['nullable', 'string', 'max:120'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'string', 'max:500'],
            'og_image_file' => ['nullable', 'image', 'max:8192'],
            'twitter_title' => ['nullable', 'string', 'max:255'],
            'twitter_description' => ['nullable', 'string', 'max:500'],
            'twitter_card' => ['nullable', 'string', 'max:80'],
            'twitter_image' => ['nullable', 'string', 'max:500'],
            'twitter_image_file' => ['nullable', 'image', 'max:8192'],
            'breadcrumb_title' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function applySlug(array $validated, ?BlogPost $post = null): array
    {
        if (! empty($validated['slug'])) {
            $validated['slug'] = Str::slug(trim($validated['slug']));
        } else {
            $base = Str::slug($validated['title'] ?? ($post->title ?? 'post'));
            $slug = $base !== '' ? $base : 'post';
            $i = 1;
            while (
                BlogPost::query()
                    ->when($post, fn ($q) => $q->where('id', '!=', $post->id))
                    ->where('slug', $slug)
                    ->exists()
            ) {
                $slug = $base . '-' . $i;
                $i++;
            }
            $validated['slug'] = $slug;
        }

        return $validated;
    }

    private function applyFeaturedMedia(Request $request, array $validated, ?BlogPost $post): array
    {
        unset($validated['featured_image'], $validated['remove_featured_image'], $validated['category_ids'], $validated['tag_ids'], $validated['new_tags']);

        if ($request->boolean('remove_featured_image') && $post?->featured_image) {
            public_storage_delete($post->featured_image);
            $validated['featured_image'] = null;
        }

        if ($request->hasFile('featured_image')) {
            if ($post?->featured_image) {
                public_storage_delete($post->featured_image);
            }
            $validated['featured_image'] = public_storage_store_upload($request->file('featured_image'), 'blog/featured');
        }

        $source = trim((string) ($validated['featured_image_source'] ?? ''));
        $validated['featured_image_source'] = $source !== '' ? $source : null;

        return $validated;
    }

    private function applySeoMedia(Request $request, array $validated): array
    {
        unset($validated['og_image_file'], $validated['twitter_image_file']);

        if ($request->hasFile('og_image_file')) {
            $path = public_storage_store_upload($request->file('og_image_file'), 'blog/seo');
            $validated['og_image'] = public_storage_url($path) ?: $path;
        } else {
            $og = trim((string) ($validated['og_image'] ?? ''));
            $validated['og_image'] = $og !== '' ? $og : null;
        }

        if ($request->hasFile('twitter_image_file')) {
            $path = public_storage_store_upload($request->file('twitter_image_file'), 'blog/seo');
            $validated['twitter_image'] = public_storage_url($path) ?: $path;
        } else {
            $tw = trim((string) ($validated['twitter_image'] ?? ''));
            $validated['twitter_image'] = $tw !== '' ? $tw : null;
        }

        return $validated;
    }

    private function syncTaxonomy(Request $request, BlogPost $post): void
    {
        $post->categories()->sync($request->input('category_ids', []));

        $tagIds = array_map('intval', $request->input('tag_ids', []));
        $newTags = (string) $request->input('new_tags', '');
        foreach (preg_split('/[,]+/', $newTags) ?: [] as $name) {
            $name = trim($name);
            if ($name === '') {
                continue;
            }
            $slug = Str::slug($name);
            if ($slug === '') {
                continue;
            }
            $tag = BlogTag::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
            $tagIds[] = (int) $tag->id;
        }

        $post->tags()->sync(array_values(array_unique($tagIds)));
    }
}
