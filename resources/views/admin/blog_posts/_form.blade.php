@php
    $selectedCategoryIds = array_map('intval', (array) old('category_ids', $post->exists ? $post->categories->pluck('id')->all() : []));
    $selectedTagIds = array_map('intval', (array) old('tag_ids', $post->exists ? $post->tags->pluck('id')->all() : []));
    $featuredPreview = $post->featured_image
        ? public_storage_url($post->featured_image)
        : ($post->featured_image_source ?: null);
@endphp

<div class="space-y-6">
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Content</h2>
        <div class="space-y-1.5">
            <label for="title" class="block text-sm text-slate-700 dark:text-slate-300">Title *</label>
            <input id="title" name="title" type="text" value="{{ old('title', $post->title) }}" required class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label for="slug" class="block text-sm text-slate-700 dark:text-slate-300">Slug</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug', $post->slug) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="Auto from title" />
            </div>
            <div class="space-y-1.5">
                <label for="published_at" class="block text-sm text-slate-700 dark:text-slate-300">Published at</label>
                <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
            </div>
        </div>
        <div class="space-y-1.5">
            <label for="status" class="block text-sm text-slate-700 dark:text-slate-300">Status *</label>
            <select id="status" name="status" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">
                <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
        </div>
        <div class="space-y-1.5">
            <label for="excerpt" class="block text-sm text-slate-700 dark:text-slate-300">Excerpt</label>
            <textarea id="excerpt" name="excerpt" rows="3" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('excerpt', $post->excerpt) }}</textarea>
        </div>
        <div class="space-y-1.5">
            <label class="block text-sm text-slate-700 dark:text-slate-300">Content</label>
            <p class="text-xs text-slate-500 mb-2">Use the image button in the toolbar to upload images into the article.</p>
            <div class="richtext-wrap bg-slate-50 dark:bg-slate-950/60 rounded-lg border border-slate-300 dark:border-slate-700 min-h-[280px]">
                <textarea name="content" id="blog_content" rows="10" class="richtext hidden">{{ old('content', $post->content) }}</textarea>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Featured media</h2>
        @if ($featuredPreview)
            <div class="mb-2">
                <img src="{{ $featuredPreview }}" alt="" class="max-h-40 rounded-lg border border-slate-200 dark:border-slate-700 object-contain bg-slate-100 dark:bg-slate-950" />
            </div>
            @if ($post->featured_image)
                <label class="inline-flex items-center gap-2 text-xs text-rose-600 cursor-pointer">
                    <input type="checkbox" name="remove_featured_image" value="1" class="rounded border-slate-400" /> Remove uploaded image
                </label>
            @endif
        @endif
        <div class="space-y-1.5">
            <label for="featured_image" class="block text-sm text-slate-700 dark:text-slate-300">Upload image</label>
            <input id="featured_image" name="featured_image" type="file" accept="image/*" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-500/20 file:px-3 file:py-1.5 file:text-xs" />
        </div>
        <div class="space-y-1.5">
            <label for="featured_image_source" class="block text-sm text-slate-700 dark:text-slate-300">Or image URL</label>
            <input id="featured_image_source" name="featured_image_source" type="url" value="{{ old('featured_image_source', $post->featured_image_source) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="https://..." />
            <p class="text-xs text-slate-500">Upload takes priority when both are set. External URL is used as fallback / WP source.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Categories &amp; tags</h2>
        <div class="space-y-2">
            <p class="text-sm text-slate-700 dark:text-slate-300">Categories</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto">
                @forelse ($categories as $cat)
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCategoryIds, true) ? 'checked' : '' }} class="rounded border-slate-400" />
                        {{ $cat->name }}
                    </label>
                @empty
                    <p class="text-xs text-slate-500">No categories yet. <a href="{{ route('admin.blog-categories.create') }}" class="text-emerald-600">Add one</a>.</p>
                @endforelse
            </div>
        </div>
        <div class="space-y-2">
            <p class="text-sm text-slate-700 dark:text-slate-300">Tags</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                @foreach ($tags as $tag)
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" {{ in_array($tag->id, $selectedTagIds, true) ? 'checked' : '' }} class="rounded border-slate-400" />
                        {{ $tag->name }}
                    </label>
                @endforeach
            </div>
            <div class="space-y-1.5 pt-2">
                <label for="new_tags" class="block text-sm text-slate-700 dark:text-slate-300">Add new tags</label>
                <input id="new_tags" name="new_tags" type="text" value="{{ old('new_tags') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="Comma separated, e.g. DHA, Investment" />
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/80 p-5 shadow-lg space-y-4">
        <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">SEO</h2>
        <div class="space-y-1.5">
            <label for="meta_title" class="block text-sm text-slate-700 dark:text-slate-300">Meta title</label>
            <input id="meta_title" name="meta_title" type="text" value="{{ old('meta_title', $post->meta_title) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
        </div>
        <div class="space-y-1.5">
            <label for="meta_description" class="block text-sm text-slate-700 dark:text-slate-300">Meta description</label>
            <textarea id="meta_description" name="meta_description" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('meta_description', $post->meta_description) }}</textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label for="meta_keywords" class="block text-sm text-slate-700 dark:text-slate-300">Meta keywords</label>
                <input id="meta_keywords" name="meta_keywords" type="text" value="{{ old('meta_keywords', $post->meta_keywords) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
            </div>
            <div class="space-y-1.5">
                <label for="focus_keyphrase" class="block text-sm text-slate-700 dark:text-slate-300">Focus keyphrase</label>
                <input id="focus_keyphrase" name="focus_keyphrase" type="text" value="{{ old('focus_keyphrase', $post->focus_keyphrase) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label for="canonical_url" class="block text-sm text-slate-700 dark:text-slate-300">Canonical URL</label>
                <input id="canonical_url" name="canonical_url" type="text" value="{{ old('canonical_url', $post->canonical_url) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
            </div>
            <div class="space-y-1.5">
                <label for="meta_robots" class="block text-sm text-slate-700 dark:text-slate-300">Robots</label>
                <input id="meta_robots" name="meta_robots" type="text" value="{{ old('meta_robots', $post->meta_robots) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="index, follow" />
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label for="og_title" class="block text-sm text-slate-700 dark:text-slate-300">OG title</label>
                <input id="og_title" name="og_title" type="text" value="{{ old('og_title', $post->og_title) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm text-slate-700 dark:text-slate-300">OG image</label>
                @php
                    $ogPreview = old('og_image', $post->og_image);
                    if ($ogPreview && ! str_starts_with((string) $ogPreview, 'http') && ! str_starts_with((string) $ogPreview, '//')) {
                        $ogPreview = public_storage_url(ltrim((string) $ogPreview, '/')) ?: $ogPreview;
                    }
                @endphp
                @if ($ogPreview)
                    <div class="mb-2"><img src="{{ $ogPreview }}" alt="" class="max-h-24 rounded-lg border border-slate-200 dark:border-slate-700 object-contain bg-slate-100 dark:bg-slate-950" /></div>
                @endif
                <input id="og_image_file" name="og_image_file" type="file" accept="image/*" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-500/20 file:px-3 file:py-1.5 file:text-xs" />
                <input id="og_image" name="og_image" type="text" value="{{ old('og_image', $post->og_image) }}" class="mt-2 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="Or paste image URL" />
                <p class="text-xs text-slate-500">Upload or URL. Upload takes priority when both are set.</p>
            </div>
        </div>
        <div class="space-y-1.5">
            <label for="og_description" class="block text-sm text-slate-700 dark:text-slate-300">OG description</label>
            <textarea id="og_description" name="og_description" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('og_description', $post->og_description) }}</textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label for="twitter_title" class="block text-sm text-slate-700 dark:text-slate-300">Twitter title</label>
                <input id="twitter_title" name="twitter_title" type="text" value="{{ old('twitter_title', $post->twitter_title) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm text-slate-700 dark:text-slate-300">Twitter image</label>
                @php
                    $twPreview = old('twitter_image', $post->twitter_image);
                    if ($twPreview && ! str_starts_with((string) $twPreview, 'http') && ! str_starts_with((string) $twPreview, '//')) {
                        $twPreview = public_storage_url(ltrim((string) $twPreview, '/')) ?: $twPreview;
                    }
                @endphp
                @if ($twPreview)
                    <div class="mb-2"><img src="{{ $twPreview }}" alt="" class="max-h-24 rounded-lg border border-slate-200 dark:border-slate-700 object-contain bg-slate-100 dark:bg-slate-950" /></div>
                @endif
                <input id="twitter_image_file" name="twitter_image_file" type="file" accept="image/*" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-500/20 file:px-3 file:py-1.5 file:text-xs" />
                <input id="twitter_image" name="twitter_image" type="text" value="{{ old('twitter_image', $post->twitter_image) }}" class="mt-2 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" placeholder="Or paste image URL" />
                <p class="text-xs text-slate-500">Upload or URL. Upload takes priority when both are set.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label for="twitter_description" class="block text-sm text-slate-700 dark:text-slate-300">Twitter description</label>
                <textarea id="twitter_description" name="twitter_description" rows="2" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm">{{ old('twitter_description', $post->twitter_description) }}</textarea>
            </div>
            <div class="space-y-1.5">
                <label for="twitter_card" class="block text-sm text-slate-700 dark:text-slate-300">Twitter card</label>
                <input id="twitter_card" name="twitter_card" type="text" value="{{ old('twitter_card', $post->twitter_card ?: 'summary_large_image') }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
            </div>
        </div>
        <div class="space-y-1.5">
            <label for="breadcrumb_title" class="block text-sm text-slate-700 dark:text-slate-300">Breadcrumb title</label>
            <input id="breadcrumb_title" name="breadcrumb_title" type="text" value="{{ old('breadcrumb_title', $post->breadcrumb_title) }}" class="block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/60 px-3 py-2.5 text-sm" />
        </div>
    </div>
</div>

@push('scripts')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {
    var uploadUrl = @json(route('admin.blog-media.upload'));
    var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || @json(csrf_token());

    function uploadImage(file) {
        var fd = new FormData();
        fd.append('file', file);
        fd.append('context', 'content');
        return fetch(uploadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: fd,
            credentials: 'same-origin'
        }).then(function (res) { return res.json(); });
    }

    var ta = document.getElementById('blog_content');
    if (!ta || typeof Quill === 'undefined') return;
    var wrap = ta.closest('.richtext-wrap');
    var div = document.createElement('div');
    div.style.minHeight = '260px';
    wrap.insertBefore(div, ta);

    var quill = new Quill(div, {
        theme: 'snow',
        modules: {
            toolbar: {
                container: [
                    [{ header: [2, 3, false] }],
                    ['bold', 'italic', 'underline', 'link'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['blockquote', 'image'],
                    ['clean']
                ],
                handlers: {
                    image: function () {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');
                        input.click();
                        input.onchange = function () {
                            var file = input.files && input.files[0];
                            if (!file) return;
                            uploadImage(file).then(function (data) {
                                if (!data || !data.success || !data.url) {
                                    alert((data && data.message) || 'Image upload failed.');
                                    return;
                                }
                                var range = quill.getSelection(true) || { index: quill.getLength() };
                                quill.insertEmbed(range.index, 'image', data.url, 'user');
                                quill.setSelection(range.index + 1);
                            }).catch(function () {
                                alert('Image upload failed.');
                            });
                        };
                    }
                }
            }
        }
    });
    quill.root.innerHTML = ta.value || '';
    quill.on('text-change', function () {
        ta.value = quill.root.innerHTML;
    });
})();
</script>
@endpush
