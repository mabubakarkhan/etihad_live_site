<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $posts = BlogPost::query()
            ->published()
            ->with(['categories:id,name,slug', 'author:id,name'])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->appends($request->except('ajax'));

        if ($this->wantsAjaxList($request)) {
            return $this->ajaxListResponse($posts);
        }

        $categories = $this->categoriesForSidebar();
        $tags = $this->tagsForSidebar();
        $cmsPage = CmsPage::findBySlug('blogs');

        return view('blog.index', compact('posts', 'categories', 'tags', 'cmsPage'));
    }

    public function show(string $year, string $month, string $day, string $slug): View
    {
        $post = BlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->with(['categories:id,name,slug', 'tags:id,name,slug', 'author:id,name'])
            ->firstOrFail();

        if (! $post->published_at
            || $post->published_at->format('Y') !== $year
            || $post->published_at->format('m') !== $month
            || $post->published_at->format('d') !== $day
        ) {
            abort(404);
        }

        $categoryIds = $post->categories->pluck('id')->all();

        $recentInCategory = BlogPost::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->when(
                $categoryIds !== [],
                fn ($q) => $q->whereHas('categories', fn ($cq) => $cq->whereIn('blog_categories.id', $categoryIds)),
                fn ($q) => $q
            )
            ->with(['categories:id,name,slug'])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $sidebarCategories = $this->categoriesForSidebar();

        return view('blog.show', compact('post', 'recentInCategory', 'sidebarCategories'));
    }

    public function category(Request $request, string $slug): View|JsonResponse
    {
        $category = BlogCategory::query()->where('slug', $slug)->firstOrFail();

        $posts = BlogPost::query()
            ->published()
            ->whereHas('categories', fn ($q) => $q->where('blog_categories.id', $category->id))
            ->with(['categories:id,name,slug', 'author:id,name'])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->appends($request->except('ajax'));

        if ($this->wantsAjaxList($request)) {
            return $this->ajaxListResponse($posts);
        }

        $categories = $this->categoriesForSidebar();
        $tags = $this->tagsForSidebar();

        return view('blog.category', compact('category', 'posts', 'categories', 'tags'));
    }

    public function tag(Request $request, string $slug): View|JsonResponse
    {
        $tag = BlogTag::query()->where('slug', $slug)->firstOrFail();

        $posts = BlogPost::query()
            ->published()
            ->whereHas('tags', fn ($q) => $q->where('blog_tags.id', $tag->id))
            ->with(['categories:id,name,slug', 'author:id,name'])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->appends($request->except('ajax'));

        if ($this->wantsAjaxList($request)) {
            return $this->ajaxListResponse($posts);
        }

        $categories = $this->categoriesForSidebar();
        $tags = $this->tagsForSidebar();

        return view('blog.tag', compact('tag', 'posts', 'categories', 'tags'));
    }

    private function wantsAjaxList(Request $request): bool
    {
        return $request->ajax() || $request->boolean('ajax');
    }

    private function ajaxListResponse($posts): JsonResponse
    {
        $query = request()->except('ajax');
        $url = url()->current();
        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return response()->json([
            'html' => view('blog.partials.posts-list', compact('posts'))->render(),
            'url' => $url,
            'page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
        ]);
    }

    private function categoriesForSidebar()
    {
        return BlogCategory::query()
            ->whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts as posts_count' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    }

    private function tagsForSidebar()
    {
        return BlogTag::query()
            ->whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts as posts_count' => fn ($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->limit(40)
            ->get(['id', 'name', 'slug']);
    }
}
