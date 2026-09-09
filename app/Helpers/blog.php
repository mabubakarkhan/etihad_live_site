<?php

use App\Models\BlogPost;

if (! function_exists('blog_post_image')) {
    function blog_post_image(?BlogPost $post, ?string $fallback = null): string
    {
        if (! $post) {
            return $fallback ?? asset('theme/images/bg/8.jpg');
        }

        return $post->displayImage($fallback);
    }
}

if (! function_exists('blog_enrich_internal_links')) {
    /**
     * Turn internal blog post <a> links into preview cards (banner + caption/link).
     */
    function blog_enrich_internal_links(?string $html, ?int $excludePostId = null): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        return (string) preg_replace_callback(
            '/<a\b([^>]*?\bhref\s*=\s*(["\'])(.*?)\2[^>]*)>(.*?)<\/a>/is',
            static function (array $matches) use ($excludePostId): string {
                $attrs = $matches[1];
                $href = html_entity_decode(trim($matches[3]), ENT_QUOTES | ENT_HTML5);
                $inner = $matches[4];
                $caption = trim(html_entity_decode(strip_tags($inner), ENT_QUOTES | ENT_HTML5));

                $post = blog_resolve_internal_post_from_url($href);
                if (! $post || ($excludePostId !== null && (int) $post->id === (int) $excludePostId)) {
                    return $matches[0];
                }

                // Skip links that already wrap only an image (avoid nested cards).
                if (preg_match('/^\s*<img\b/i', $inner) && ! preg_match('/[a-z0-9]/i', strip_tags($inner))) {
                    return $matches[0];
                }

                $url = e($post->url());
                $title = e($post->title);
                $image = e($post->displayImage());
                $label = $caption !== '' ? e($caption) : $title;

                return '<figure class="blog-internal-link-card">'
                    . '<a class="blog-internal-link-card__media" href="' . $url . '" title="' . $title . '">'
                    . '<img src="' . $image . '" alt="' . $title . '" loading="lazy" width="960" height="540">'
                    . '</a>'
                    . '<figcaption class="blog-internal-link-card__caption">'
                    . '<a href="' . $url . '"' . (str_contains($attrs, 'target=') ? ' target="_blank" rel="noopener noreferrer"' : '') . '>'
                    . $label
                    . '</a>'
                    . '</figcaption>'
                    . '</figure>';
            },
            $html
        );
    }
}

if (! function_exists('blog_resolve_internal_post_from_url')) {
    function blog_resolve_internal_post_from_url(string $href): ?BlogPost
    {
        $href = trim($href);
        if ($href === '' || str_starts_with($href, '#') || str_starts_with(strtolower($href), 'mailto:') || str_starts_with(strtolower($href), 'tel:')) {
            return null;
        }

        $path = $href;
        if (preg_match('#^https?://#i', $href) || str_starts_with($href, '//')) {
            $parts = parse_url($href);
            if (! is_array($parts) || empty($parts['path'])) {
                return null;
            }

            $appHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
            $linkHost = strtolower((string) ($parts['host'] ?? ''));
            if ($appHost !== '' && $linkHost !== '' && $appHost !== $linkHost) {
                return null;
            }

            $path = (string) $parts['path'];
        }

        $path = '/' . ltrim($path, '/');
        $path = preg_replace('#/+#', '/', $path) ?: $path;

        // Match /{Y}/{m}/{d}/{slug} optionally with a subdirectory prefix (e.g. /etihad/public/...).
        if (! preg_match('#(?:^|/)(\d{4})/(\d{2})/(\d{2})/([A-Za-z0-9\-_]+)/?(?:[?#].*)?$#', $path, $m)) {
            return null;
        }

        $year = $m[1];
        $month = $m[2];
        $day = $m[3];
        $slug = $m[4];

        $post = BlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->first();

        if (! $post || ! $post->published_at) {
            return null;
        }

        if (
            $post->published_at->format('Y') !== $year
            || $post->published_at->format('m') !== $month
            || $post->published_at->format('d') !== $day
        ) {
            // Still accept slug match if date in URL is stale but post exists.
            return $post;
        }

        return $post;
    }
}
