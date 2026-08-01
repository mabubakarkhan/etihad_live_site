<?php

if (! function_exists('blog_post_image')) {
    function blog_post_image(?\App\Models\BlogPost $post, ?string $fallback = null): string
    {
        if (! $post) {
            return $fallback ?? asset('theme/images/bg/8.jpg');
        }

        return $post->displayImage($fallback);
    }
}
