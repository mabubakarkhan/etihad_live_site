<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ ($cmsPage && $cmsPage->meta_title) ? $cmsPage->meta_title : config('app.name') }}</title>
        @php
            $homeTitle = ($cmsPage && $cmsPage->meta_title) ? $cmsPage->meta_title : config('app.name');
            $homeSeo = seo_from_record($cmsPage ?? null, [
                'title' => $homeTitle,
                'canonical' => url('/'),
            ]);
        @endphp
        @include('partials.seo-meta', ['seo' => $homeSeo])
    </head>
    <body style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; margin: 0; padding: 0; background: #f5f5f5;">
        <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
            <div style="background: #ffffff; padding: 32px 40px; border-radius: 12px; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15); max-width: 520px; width: 100%; text-align: center;">
                <h1 style="margin: 0 0 12px; font-size: 28px; letter-spacing: 0.04em; text-transform: uppercase; color: #0f172a;">
                    Etihad
                </h1>
                <p style="margin: 0 0 16px; font-size: 14px; color: #64748b;">
                    Laravel is installed and your landing page is ready.
                </p>
                <p style="margin: 0; font-size: 13px; color: #94a3b8;">
                    You can now start building your application in Laravel.
                </p>
            </div>
        </div>
    </body>
</html>
