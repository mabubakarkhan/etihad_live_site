<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\QueryException;
use App\Models\Property;
use App\Models\ProjectType;
use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\HomepageHeroSettingController;
use App\Http\Controllers\HomepageDhaSectionController;
use App\Http\Controllers\HomepageDealersSectionController;
use App\Support\SafeMigrationRunner;
use App\Http\Controllers\HomepageChoiceController;
use App\Http\Controllers\HomepageAchievementsController;
use App\Http\Controllers\HomepageWhatSetsApartController;
use App\Http\Controllers\HomepageInvestmentJourneyController;
use App\Http\Controllers\HomepageAboutSettingController;
use App\Http\Controllers\HomepageVisionSettingController;
use App\Http\Controllers\HomepageWhySettingController;
use App\Http\Controllers\PortalHeroSlideController;
use App\Http\Controllers\PortalAdController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ListingSortController;
use App\Http\Controllers\DhaSettingController;
use App\Http\Controllers\DhaPhaseController;
use App\Models\DhaSetting;
use App\Models\DhaPhase;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SiteSeoSettingsController;
use App\Http\Controllers\ContactSettingsController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\SellRentLeadController;
use App\Http\Controllers\PropertyRequestController;
use App\Http\Controllers\CmsPageController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\AdminNotificationController;
use App\Models\PropertyRequest;
use App\Models\HomepageHeroSetting;
use App\Models\HomepageDhaSectionSetting;
use App\Models\HomepageDealersSectionSetting;
use App\Models\HomepageLocationSectionSetting;
use App\Models\HomepageChoiceSetting;
use App\Models\HomepageAchievementsSetting;
use App\Models\HomepageWhatSetsApartSetting;
use App\Models\HomepageInvestmentJourneySetting;
use App\Models\HomepageAboutSetting;
use App\Models\HomepageVisionSetting;
use App\Models\HomepageWhySetting;
use App\Models\PortalHeroSlide;
use App\Models\PortalAd;
use App\Models\Partner;
use App\Models\SiteSeoSetting;
use App\Models\Project;
use App\Models\VisitorDailyCount;
use App\Models\CmsPage;
use App\Models\Dealer;
use App\Models\ContactSetting;
use App\Models\Testimonial;
use App\Models\SellRentPageSetting;

if (!function_exists('db_safe')) {
    /**
     * Execute DB read safely and return a fallback if table/engine is broken.
     */
    function db_safe(string $label, callable $callback, mixed $fallback = null): mixed
    {
        try {
            return $callback();
        } catch (QueryException $e) {
            Log::warning("DB safe fallback used: {$label}", [
                'error' => $e->getMessage(),
            ]);

            return $fallback;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Run migrations via browser (for shared hosting without SSH)
|--------------------------------------------------------------------------
| Visit: https://yoursite.com/run-migrations?token=YOUR_SECRET_TOKEN
| Set MIGRATION_RUN_TOKEN in .env (e.g. a long random string).
| Remove or comment out this route after use for security.
*/
Route::get('/run-migrations', function () {
    $token = config('app.migration_run_token') ?: env('MIGRATION_RUN_TOKEN');
    $requestToken = request()->query('token');
    if (!$token || $requestToken !== $token) {
        return response()->json(['success' => false, 'message' => 'Invalid or missing token.'], 403);
    }
    try {
        $limit = max(0, (int) request()->query('limit', 0));
        $result = SafeMigrationRunner::run($limit);
        $log = $result['log'];
        $remaining = $result['remaining'];

        return response()->json([
            'success' => true,
            'message' => $remaining > 0
                ? "Migrations batch completed. {$remaining} still pending — open this URL again."
                : 'Migrations completed.',
            'remaining' => $remaining,
            'output' => implode(PHP_EOL, $log),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'output' => $e->getTraceAsString(),
        ], 500);
    }
})->name('run-migrations');

/*
|--------------------------------------------------------------------------
| Repair public storage via browser (shared hosting helper)
|--------------------------------------------------------------------------
| Visit: /run-storage-fix?token=YOUR_SECRET_TOKEN
| It attempts storage:link. If symlink is unavailable, it mirrors files
| from storage/app/public to public/storage.
*/
Route::get('/run-storage-fix', function () {
    $token = config('app.migration_run_token') ?: env('MIGRATION_RUN_TOKEN');
    $requestToken = request()->query('token');
    if (!$token || $requestToken !== $token) {
        return response()->json(['success' => false, 'message' => 'Invalid or missing token.'], 403);
    }

    $source = storage_path('app/public');
    $target = public_path('storage');
    $linked = false;
    $copied = 0;
    $errors = [];

    try {
        Artisan::call('storage:link');
    } catch (\Throwable $e) {
        $errors[] = 'storage:link failed: ' . $e->getMessage();
    }

    if (is_link($target)) {
        $linked = true;
    } else {
        try {
            $fs = app(Filesystem::class);
            if (!is_dir($target)) {
                $fs->ensureDirectoryExists($target, 0755, true);
            }
            if (is_dir($source)) {
                foreach ($fs->allFiles($source) as $file) {
                    $from = $file->getPathname();
                    $relative = ltrim(str_replace($source, '', $from), DIRECTORY_SEPARATOR);
                    $to = $target . DIRECTORY_SEPARATOR . $relative;
                    $fs->ensureDirectoryExists(dirname($to), 0755, true);
                    if (!file_exists($to)) {
                        $fs->copy($from, $to);
                        $copied++;
                    }
                }
            }
        } catch (\Throwable $e) {
            $errors[] = 'mirror failed: ' . $e->getMessage();
        }
    }

    return response()->json([
        'success' => empty($errors),
        'linked' => $linked,
        'copied_files' => $copied,
        'public_storage_path' => $target,
        'source_path' => $source,
        'errors' => $errors,
    ], empty($errors) ? 200 : 500);
})->name('run-storage-fix');

Route::get('/', function () {
    $homepagePath = public_path('homepage/index.html');

    if (! is_file($homepagePath)) {
        $cmsPage = db_safe('home.cms_page', fn () => CmsPage::findBySlug('home'));
        return view('index', compact('cmsPage'));
    }

    $html = file_get_contents($homepagePath);
    $appBase = rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/');
    $base = $appBase . '/homepage/';
    $homeUrl = url('/');

    $html = preg_replace_callback(
        '/\b(src|href)=(["\'])((?!https?:|\/\/|data:|#|__)(?:assets\/|dist\/)[^"\']+)\2/',
        static fn (array $m) => $m[1] . '=' . $m[2] . $base . $m[3] . $m[2],
        $html
    );
    $html = preg_replace_callback(
        '/\bsrcset=(["\'])((?!https?:|\/\/)(?:assets\/)[^"\']+)\1/',
        static fn (array $m) => 'srcset=' . $m[1] . $base . $m[2] . $m[1],
        $html
    );
    $html = str_replace(
        'src="../theme/js/etihad-map-styles.js"',
        'src="' . $appBase . '/theme/js/etihad-map-styles.js"',
        $html
    );

    $html = str_replace('__HOME_CANONICAL__', $homeUrl, $html);
    $html = str_replace('__HOME_URL__', $homeUrl, $html);

    $homepageHeroSetting = db_safe('home.hero_setting', fn () => HomepageHeroSetting::instance(), new HomepageHeroSetting());
    $homepageHeroImage = homepage_asset_url($homepageHeroSetting->hero_image ?? null, $base, 'hero-screen-1-D7I92d4H.webp');
    $html = str_replace('__HOMEPAGE_HERO_IMAGE__', $homepageHeroImage, $html);

    $html = str_replace('__HOMEPAGE_SIDEBAR_NAV__', View::make('partials.homepage-sidebar-nav')->render(), $html);
    $html = str_replace('__HOMEPAGE_NAVBAR_NAV__', View::make('partials.homepage-navbar-nav')->render(), $html);
    $html = str_replace('__HOMEPAGE_FOOTER_NAV__', View::make('partials.homepage-footer-nav')->render(), $html);
    $html = str_replace('__HOMEPAGE_NAVBAR_SCROLL__', View::make('partials.homepage-navbar-scroll')->render(), $html);

    $homepageDhaSection = db_safe('home.dha_section', fn () => HomepageDhaSectionSetting::instance(), new HomepageDhaSectionSetting());
    $homepageDhaPhases = db_safe('home.dha_phases_showcase', fn () => DhaPhase::active()->frontOrdered()->get(), collect());
    $html = str_replace('__HOMEPAGE_DHA_SECTION__', View::make('partials.homepage-dha-section', [
        'setting' => $homepageDhaSection,
        'phases' => $homepageDhaPhases,
    ])->render(), $html);

    $homepageProjects = db_safe('home.projects_showcase', fn () => Project::query()
        ->with(['projectTypes:id,name,slug'])
        ->active()
        ->where(function ($query) {
            $query->where(function ($inner) {
                $inner->whereNotNull('homepage_listing_image')
                    ->where('homepage_listing_image', '!=', '');
            })->orWhere(function ($inner) {
                $inner->whereNotNull('featured_image')
                    ->where('featured_image', '!=', '');
            });
        })
        ->frontOrdered()
        ->get([
            'id', 'title', 'slug', 'city', 'state', 'price', 'launch_year',
            'description', 'featured_image', 'homepage_listing_image', 'pricing_place_cards',
        ]), collect());

    $projectCardsHtml = View::make('partials.homepage-projects-showcase-cards', [
        'projects' => $homepageProjects,
    ])->render();

    $html = str_replace('__HOMEPAGE_PROJECTS_CARDS__', $projectCardsHtml, $html);

    $homepagePropertyBase = function () {
        return Property::query()
            ->with(['projectTypes:id,name,slug'])
            ->where('dealer_id', '!=', 0)
            ->whereHas('dealer', function ($q) {
                $q->where('status', Dealer::STATUS_ACTIVE);
            })
            ->active()
            ->whereNotNull('featured_image')
            ->where('featured_image', '!=', '')
            ->frontOrdered();
    };

    $popularProperties = db_safe('home.hot_offers_popular', fn () => $homepagePropertyBase()
        ->where('is_hot', true)
        ->limit(3)
        ->get(), collect());

    $residentialProperties = db_safe('home.hot_offers_residential', fn () => $homepagePropertyBase()
        ->whereHas('projectTypes', function ($q) {
            $q->whereRaw('LOWER(slug) = ?', ['residential']);
        })
        ->limit(3)
        ->get(), collect());

    $commercialProperties = db_safe('home.hot_offers_commercial', fn () => $homepagePropertyBase()
        ->whereHas('projectTypes', function ($q) {
            $q->whereRaw('LOWER(slug) = ?', ['commercial']);
        })
        ->limit(3)
        ->get(), collect());

    $hotOffersPanelsHtml = View::make('partials.homepage-hot-offers-panels', [
        'popularProperties' => $popularProperties,
        'residentialProperties' => $residentialProperties,
        'commercialProperties' => $commercialProperties,
    ])->render();

    $html = str_replace('__HOMEPAGE_HOT_OFFERS_PANELS__', $hotOffersPanelsHtml, $html);

    $homepageDealersSection = db_safe('home.dealers_section', fn () => HomepageDealersSectionSetting::instance(), new HomepageDealersSectionSetting());
    $homepageDealers = db_safe('home.dealers_showcase', fn () => Dealer::query()
        ->active()
        ->where('show_homepage', true)
        ->whereNotNull('slug')
        ->where('slug', '!=', '')
        ->withCount('properties')
        ->orderBy('name', 'asc')
        ->get(['id', 'name', 'slug', 'profile_pic', 'banner_image', 'info_detail', 'view_count', 'city', 'state']), collect());
    $html = str_replace('__HOMEPAGE_DEALERS_SECTION__', View::make('partials.homepage-dealers-section', [
        'setting' => $homepageDealersSection,
        'dealers' => $homepageDealers,
    ])->render(), $html);

    $homepageMapProperties = db_safe('home.map_properties', fn () => Property::query()
        ->with(['projectTypes:id,name'])
        ->where('dealer_id', '!=', 0)
        ->whereHas('dealer', function ($q) {
            $q->where('status', Dealer::STATUS_ACTIVE);
        })
        ->active()
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->frontOrdered()
        ->limit(100)
        ->get()
        ->map(function (Property $p) {
            $address = trim(implode(', ', array_filter([
                $p->short_address,
                $p->address,
                $p->town,
                $p->city,
                $p->state,
            ])));

            $areaParts = [];
            if ($p->area_kanal) {
                $areaParts[] = rtrim(rtrim(number_format((float) $p->area_kanal, 2), '0'), '.') . ' Kanal';
            }
            if ($p->area_marla) {
                $areaParts[] = rtrim(rtrim(number_format((float) $p->area_marla, 2), '0'), '.') . ' Marla';
            }

            $purposeLabel = $p->purpose === Property::PURPOSE_RENT ? 'Rent' : 'Sale';
            $badge = $p->projectTypes->first()?->name
                ?: ($p->is_hot ? 'Featured' : $purposeLabel);

            return [
                'id' => $p->id,
                'title' => $p->title,
                'detail_url' => route('property.show', $p->slug),
                'latitude' => (float) $p->latitude,
                'longitude' => (float) $p->longitude,
                'description' => $p->description
                    ? \Illuminate\Support\Str::limit(strip_tags($p->description), 140)
                    : $address,
                'address' => $address,
                'price' => format_price($p->price_digits, $p->price_string),
                'purpose_label' => $purposeLabel,
                'badge' => $badge,
                'size' => $areaParts ? implode(' / ', $areaParts) : '',
                'status' => $purposeLabel,
            ];
        })
        ->filter(fn (array $p) => $p['latitude'] && $p['longitude'])
        ->values(), collect());

    $homepageMapJson = $homepageMapProperties->toJson(
        JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
    );

    $html = str_replace('__HOMEPAGE_MAP_PROPERTIES_JSON__', $homepageMapJson, $html);

    $homepageTestimonials = db_safe('home.testimonials', fn () => Testimonial::query()
        ->whereNotNull('image')
        ->where('image', '!=', '')
        ->whereNotNull('comment')
        ->where('comment', '!=', '')
        ->orderBy('id')
        ->get(['id', 'name', 'image', 'comment', 'city']), collect());

    $testimonialsSlidesHtml = View::make('partials.homepage-testimonials-slides', [
        'testimonials' => $homepageTestimonials,
    ])->render();

    $html = str_replace('__HOMEPAGE_TESTIMONIALS_SLIDES__', $testimonialsSlidesHtml, $html);

    $homepageContact = db_safe('home.contact_settings', fn () => ContactSetting::instance()) ?? new ContactSetting();
    $homepageCmsPage = db_safe('home.cms_page', fn () => CmsPage::findBySlug('home'));
    $homepageSiteSeo = db_safe('home.site_seo', fn () => SiteSeoSetting::instance());
    $homepageAssetBase = $base;

    $homepageSeo = seo_homepage_bundle($homepageCmsPage, $homepageSiteSeo, [
        'title' => 'Best Real Estate Company & Property Developer Lahore | Etihad',
        'description' => 'Transform your real estate portfolio with Etihad Marketing, a leading property development and real estate company in Lahore, Pakistan offering premium residential and commercial properties.',
        'keywords' => 'Real estate Lahore, Property developer Pakistan, Residential projects Lahore, Commercial properties Lahore, DHA Lahore properties, Etihad Marketing, Premium properties Pakistan, Investment properties Lahore',
        'canonical' => $homeUrl,
        'image' => $homepageAssetBase . 'assets/og-image-C3mzMeAS.png',
        'type' => 'website',
    ]);

    $html = str_replace('__HOMEPAGE_HEAD_SEO__', View::make('partials.homepage-head-seo', ['seo' => $homepageSeo])->render(), $html);
    $html = str_replace('__HOMEPAGE_TRACKING_HEAD__', View::make('partials.homepage-tracking-head', ['siteSeo' => $homepageSiteSeo])->render(), $html);
    $html = str_replace('__HOMEPAGE_TRACKING_BODY_OPEN__', View::make('partials.homepage-tracking-body-open', ['siteSeo' => $homepageSiteSeo])->render(), $html);
    $html = str_replace('__HOMEPAGE_TRACKING_BODY_CLOSE__', View::make('partials.homepage-tracking-body-close', ['siteSeo' => $homepageSiteSeo])->render(), $html);
    $html = str_replace('__HOMEPAGE_MENU_CONTACT__', View::make('partials.homepage-menu-contact', ['cs' => $homepageContact])->render(), $html);

    $homepageLocationSection = db_safe('home.location_section', fn () => HomepageLocationSectionSetting::instance(), new HomepageLocationSectionSetting());
    $defaultLocationMapUrl = $homepageAssetBase . 'assets/location-Q0z4vYJT.webp';
    $defaultLocationPinUrl = $homepageAssetBase . 'assets/pin-Ckk56Ywx.png';
    $resolvedLocationMapUrl = homepage_asset_url($homepageLocationSection->map_background_image ?? null, $homepageAssetBase, 'location-Q0z4vYJT.webp');
    $resolvedLocationPinUrl = homepage_asset_url($homepageLocationSection->pin_image ?? null, $homepageAssetBase, 'pin-Ckk56Ywx.png');

    if ($resolvedLocationMapUrl !== $defaultLocationMapUrl) {
        $html = str_replace($defaultLocationMapUrl, $resolvedLocationMapUrl, $html);
    }

    if ($resolvedLocationPinUrl !== $defaultLocationPinUrl) {
        $html = str_replace($defaultLocationPinUrl, $resolvedLocationPinUrl, $html);
    }

    $html = str_replace('__HOMEPAGE_LOCATION_CARD__', View::make('partials.homepage-location-card', [
        'cs' => $homepageContact,
        'locationSection' => $homepageLocationSection,
        'assetBase' => $homepageAssetBase,
    ])->render(), $html);

    $html = str_replace('__HOMEPAGE_FOOTER_CONTACT__', View::make('partials.homepage-footer-contact', ['cs' => $homepageContact])->render(), $html);
    $html = str_replace('__HOMEPAGE_FOOTER_LEGAL__', View::make('partials.homepage-footer-legal')->render(), $html);
    $html = str_replace('__HOMEPAGE_FOOTER_SOCIALS__', View::make('partials.homepage-footer-socials', ['cs' => $homepageContact])->render(), $html);

    $homepageVision = db_safe('home.vision', fn () => HomepageVisionSetting::instance(), new HomepageVisionSetting());
    $html = str_replace('__HOMEPAGE_VISION_SECTION__', View::make('partials.homepage-vision-section', [
        'vision' => $homepageVision,
        'assetBase' => $base,
    ])->render(), $html);

    $homepageWhy = db_safe('home.why', fn () => HomepageWhySetting::instance(), new HomepageWhySetting());
    $html = str_replace([
        '__HOMEPAGE_WHY_IMAGE_LEFT__',
        '__HOMEPAGE_WHY_IMAGE_CENTER__',
        '__HOMEPAGE_WHY_IMAGE_RIGHT__',
        '__HOMEPAGE_WHY_IMAGE_CENTER_BACK__',
    ], [
        $homepageWhy->imageUrl('image_left', $base, 'contemporary-left-BqpaZZO6.avif'),
        $homepageWhy->imageUrl('image_center', $base, 'contemporary-center-Cy1UF1UF.avif'),
        $homepageWhy->imageUrl('image_right', $base, 'contemporary-right-BGFk98DL.avif'),
        $homepageWhy->imageUrl('image_center_back', $base, 'contemporary-center-back-MRHJVZZb.avif'),
    ], $html);
    $html = str_replace('__HOMEPAGE_WHY_SECTION__', View::make('partials.homepage-why-section', [
        'why' => $homepageWhy,
    ])->render(), $html);

    $homepageChoice = db_safe('home.choice', fn () => HomepageChoiceSetting::instance(), new HomepageChoiceSetting());
    $homepageChoiceSlides = db_safe('home.choice_slides', fn () => HomepageChoiceSetting::orderedSlides(), collect());
    $html = str_replace('__HOMEPAGE_CHOICE_SECTION__', View::make('partials.homepage-choice-section', [
        'choice' => $homepageChoice,
        'slides' => $homepageChoiceSlides,
        'assetBase' => $base,
    ])->render(), $html);

    $homepageAbout = db_safe('home.about', fn () => HomepageAboutSetting::instance(), new HomepageAboutSetting());
    $html = str_replace('__HOMEPAGE_ABOUT_HERO_SCREEN__', View::make('partials.homepage-about-hero-screen', [
        'about' => $homepageAbout,
        'assetBase' => $base,
        'heroImage' => $homepageHeroImage,
    ])->render(), $html);
    $html = str_replace('__HOMEPAGE_ABOUT_MOBILE_SECTION__', View::make('partials.homepage-about-mobile', [
        'about' => $homepageAbout,
        'assetBase' => $base,
    ])->render(), $html);

    $homepageJourney = db_safe('home.investment_journey', fn () => HomepageInvestmentJourneySetting::instance(), new HomepageInvestmentJourneySetting());
    $homepageJourneySteps = db_safe('home.investment_journey_steps', fn () => HomepageInvestmentJourneySetting::orderedSteps(), collect());
    $html = str_replace('__HOMEPAGE_INVESTMENT_JOURNEY_SECTION__', View::make('partials.homepage-investment-journey-section', [
        'journey' => $homepageJourney,
        'steps' => $homepageJourneySteps,
    ])->render(), $html);

    $homepageApart = db_safe('home.what_sets_apart', fn () => HomepageWhatSetsApartSetting::instance(), new HomepageWhatSetsApartSetting());
    $homepageApartCards = db_safe('home.what_sets_apart_cards', fn () => HomepageWhatSetsApartSetting::orderedCards(), collect());
    $html = str_replace('__HOMEPAGE_WHAT_SETS_APART_SECTION__', View::make('partials.homepage-what-sets-apart-section', [
        'apart' => $homepageApart,
        'cards' => $homepageApartCards,
    ])->render(), $html);

    $homepageAchievements = db_safe('home.achievements', fn () => HomepageAchievementsSetting::instance(), new HomepageAchievementsSetting());
    $homepageAchievementStats = db_safe('home.achievement_stats', fn () => HomepageAchievementsSetting::orderedStats(), collect());
    $html = str_replace('__HOMEPAGE_ACHIEVEMENTS_SECTION__', View::make('partials.homepage-achievements-section', [
        'achievements' => $homepageAchievements,
        'stats' => $homepageAchievementStats,
    ])->render(), $html);

    $html = preg_replace_callback(
        '/\b(src|href)=(["\'])((?!https?:|\/\/|data:|#|__)(?:assets\/|dist\/)[^"\']+)\2/',
        static fn (array $m) => $m[1] . '=' . $m[2] . $base . $m[3] . $m[2],
        $html
    );
    $html = preg_replace_callback(
        '/\bsrcset=(["\'])((?!https?:|\/\/)(?:assets\/)[^"\']+)\1/',
        static fn (array $m) => 'srcset=' . $m[1] . $base . $m[2] . $m[1],
        $html
    );

    return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
});

Route::get('/portal', function () {
    $projectTypes = db_safe('portal.project_types', fn () => ProjectType::orderBy('name')->get(['id', 'name', 'slug']), collect());
    $lahoreCityId = db_safe('portal.lahore_city', fn () => City::whereRaw('LOWER(name) = ?', ['lahore'])->value('id'));
    $dhaPhases = db_safe('portal.dha_phases', fn () => DhaPhase::active()->frontOrdered()->get(['id', 'title', 'slug', 'featured_image', 'card_image', 'sort_order']), collect());

    $hotPropertyCards = db_safe('portal.hot_properties', fn () => Property::query()
        ->with(['projectTypes:id,name', 'dealer:id,name,profile_pic,slug'])
        ->where('is_hot', true)
        ->where('dealer_id', '!=', 0)
        ->whereHas('dealer', function ($q) {
            $q->where('status', Dealer::STATUS_ACTIVE);
        })
        ->active()
        ->frontOrdered()
        ->limit(12)
        ->get()
        ->map(function (Property $p) {
            $gallery = is_array($p->gallery) ? $p->gallery : [];
            $photoCount = count($gallery) + ($p->featured_image ? 1 : 0);
            $purposeLabel = $p->purpose === Property::PURPOSE_RENT ? 'Rent' : 'Sale';
            $filterClass = $p->purpose === Property::PURPOSE_RENT ? 'cat-rent' : 'cat-sale';

            return [
                'id' => $p->id,
                'title' => $p->title,
                'detail_url' => route('property.show', $p->slug),
                'featured_image_url' => $p->featured_image
                    ? url('storage/' . ltrim($p->featured_image, '/'))
                    : asset('theme/images/all/1.jpg'),
                'short_address' => $p->short_address,
                'purpose_label' => $purposeLabel,
                'property_type' => $p->property_type ? ucfirst(str_replace('_', ' ', $p->property_type)) : null,
                'project_type_names' => $p->projectTypes->pluck('name')->values()->all(),
                'price' => format_price($p->price_digits, $p->price_string),
                'bedrooms' => $p->bedrooms ?? 0,
                'bathrooms' => $p->bathrooms ?? 0,
                'kitchen' => $p->kitchen ?? 0,
                'photo_count' => $photoCount,
                'dealer_name' => $p->dealer?->name ?? 'Etihad Marketing',
                'dealer_image_url' => $p->dealer && $p->dealer->profile_pic
                    ? url('storage/' . ltrim($p->dealer->profile_pic, '/'))
                    : asset('theme/images/avatar/1.jpg'),
                'dealer_url' => dealer_profile_url($p->dealer),
                'excerpt' => $p->description ? \Illuminate\Support\Str::limit(strip_tags($p->description), 120) : '',
                'purpose' => $p->purpose,
                'filter_class' => $filterClass,
            ];
        }), collect());

    $portalCarouselProjects = db_safe('portal.carousel_projects', fn () => Project::query()
        ->with(['projectTypes:id,name,slug'])
        ->active()
        ->whereNotNull('homepage_listing_image')
        ->where('homepage_listing_image', '!=', '')
        ->frontOrdered()
        ->limit(12)
        ->get([
            'id', 'title', 'slug', 'city', 'state', 'price', 'launch_year',
            'short_address', 'full_address', 'featured_image', 'homepage_listing_image',
            'pricing_place_cards',
        ]), collect());

    $portalMapProperties = db_safe('portal.map_properties', fn () => Property::query()
        ->with(['projectTypes:id,name', 'dealer:id,status'])
        ->where('dealer_id', '!=', 0)
        ->whereHas('dealer', function ($q) {
            $q->where('status', Dealer::STATUS_ACTIVE);
        })
        ->active()
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->frontOrdered()
        ->limit(200)
        ->get()
        ->map(function (Property $p) {
            return [
                'id' => $p->id,
                'title' => $p->title,
                'detail_url' => route('property.show', $p->slug),
                'short_address' => $p->short_address,
                'address' => $p->address,
                'town' => $p->town,
                'city' => $p->city,
                'state' => $p->state,
                'purpose_label' => $p->purpose === Property::PURPOSE_RENT ? 'Rent' : 'Sale',
                'project_type_names' => $p->projectTypes->pluck('name')->values()->all(),
                'price' => format_price($p->price_digits, $p->price_string),
                'latitude' => $p->latitude,
                'longitude' => $p->longitude,
            ];
        }), collect());

    $portalDealers = db_safe('portal.dealers_strip', fn () => Dealer::query()
        ->active()
        ->whereNotNull('slug')
        ->where('slug', '!=', '')
        ->orderBy('name', 'asc')
        ->limit(24)
        ->get(['id', 'name', 'slug', 'profile_pic']), collect());

    $portalPopularDealers = db_safe('portal.popular_dealers', fn () => Dealer::query()
        ->active()
        ->where('show_homepage', true)
        ->whereNotNull('slug')
        ->where('slug', '!=', '')
        ->withCount('properties')
        ->orderBy('name', 'asc')
        ->limit(4)
        ->get(['id', 'name', 'slug', 'profile_pic', 'info_detail', 'view_count']), collect());

    $portalHomepageAdDealers = db_safe('portal.homepage_ad_dealers', fn () => Dealer::query()
        ->active()
        ->where('show_homepage_ad', true)
        ->whereNotNull('profile_pic')
        ->where('profile_pic', '!=', '')
        ->orderBy('name', 'asc')
        ->limit(5)
        ->get(['id', 'name', 'profile_pic']), collect());

    $portalPartners = db_safe('portal.partners', fn () => Partner::query()
        ->whereNotNull('image')
        ->where('image', '!=', '')
        ->orderBy('id')
        ->get(['id', 'title', 'image']), collect());

    $portalTestimonials = db_safe('portal.testimonials', fn () => Testimonial::query()
        ->whereNotNull('image')
        ->where('image', '!=', '')
        ->whereNotNull('comment')
        ->where('comment', '!=', '')
        ->orderBy('id')
        ->get(['id', 'name', 'image', 'comment', 'city']), collect());

    $portalHeroSlides = db_safe('portal.hero_slides', fn () => PortalHeroSlide::query()
        ->active()
        ->whereNotNull('image')
        ->where('image', '!=', '')
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get(['id', 'image']), collect());

    $portalAds = db_safe('portal.ads', fn () => PortalAd::query()
        ->active()
        ->whereIn('slug', ['properties', 'dealers'])
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get(['id', 'slug', 'title', 'image']), collect());

    $cmsPage = db_safe('portal.cms_page', fn () => CmsPage::findBySlug('home'));

    return view('portal', compact('projectTypes', 'lahoreCityId', 'dhaPhases', 'hotPropertyCards', 'portalCarouselProjects', 'portalMapProperties', 'portalDealers', 'portalPopularDealers', 'portalHomepageAdDealers', 'portalPartners', 'portalTestimonials', 'portalHeroSlides', 'portalAds', 'cmsPage'));
})->name('portal');

Route::get('/listing', function () {
    $projectTypes = db_safe('listing.project_types', fn () => ProjectType::orderBy('name')->get(['id', 'name', 'slug']), collect());
    $cmsPage = db_safe('listing.cms_page', fn () => CmsPage::findBySlug('listing'));
    $lahoreCityId = db_safe('listing.lahore_city', fn () => City::whereRaw('LOWER(name) = ?', ['lahore'])->value('id'));
    $dhaPhases = db_safe('listing.dha_phases', fn () => DhaPhase::active()->frontOrdered()->get(['id', 'title', 'slug']), collect());
    $dhaPhaseUrls = $dhaPhases->mapWithKeys(fn ($p) => [(string) $p->id => route('dha.phase.show', $p->slug)])->all();
    // Front: “Listing” URL shows dealer listings only (own listings stay admin-only).
    return view('listing', ['projectTypes' => $projectTypes, 'cmsPage' => $cmsPage, 'lahoreCityId' => $lahoreCityId, 'dhaPhases' => $dhaPhases, 'dhaPhaseUrls' => $dhaPhaseUrls]);
})->name('listing');

Route::get('/dha', function () {
    $dha = db_safe('dha.settings', fn () => DhaSetting::instance());
    if (!$dha || $dha->status !== DhaSetting::STATUS_ACTIVE) {
        abort(404);
    }
    $phases = db_safe('dha.phases', fn () => DhaPhase::active()->frontOrdered()->get(), collect());

    return view('dha', compact('dha', 'phases'));
})->name('dha.index');

Route::get('/dha/{phase:slug}', function (DhaPhase $phase) {
    if ($phase->status !== DhaPhase::STATUS_ACTIVE) {
        abort(404);
    }
    $phase->load(['projectTypes:id,name,slug']);
    $projectTypes = db_safe('dha_phase.project_types', fn () => ProjectType::orderBy('name')->get(['id', 'name', 'slug']), collect());
    $dhaPhases = db_safe('dha_phase.dha_phases', fn () => DhaPhase::active()->frontOrdered()->get(['id', 'title', 'slug']), collect());
    $lahoreCityId = db_safe('dha_phase.lahore_city', fn () => City::whereRaw('LOWER(name) = ?', ['lahore'])->value('id'));
    $dhaPhaseUrls = $dhaPhases->mapWithKeys(fn ($p) => [(string) $p->id => route('dha.phase.show', $p->slug)])->all();
    $hasPhaseListings = db_safe(
        'dha_phase.' . $phase->id . '.listings',
        fn () => Property::query()
            ->where('dha_phase_id', $phase->id)
            ->where('dealer_id', '!=', 0)
            ->whereHas('dealer', function ($q) {
                $q->where('status', Dealer::STATUS_ACTIVE);
            })
            ->active()
            ->exists(),
        false
    );

    return view('dha-phase', compact('phase', 'projectTypes', 'dhaPhases', 'lahoreCityId', 'dhaPhaseUrls', 'hasPhaseListings'));
})->name('dha.phase.show');

Route::get('/dha/{phase:slug}/map', function (DhaPhase $phase) {
    if ($phase->status !== DhaPhase::STATUS_ACTIVE || ! $phase->showMapButton()) {
        abort(404);
    }

    return view('dha-phase-map', compact('phase'));
})->name('dha.phase.map');

Route::get('/dha/{phase:slug}/vr-tour', function (DhaPhase $phase) {
    if ($phase->status !== DhaPhase::STATUS_ACTIVE || ! $phase->hasVrTour()) {
        abort(404);
    }
    $vrTourUrl = $phase->vrTourUrl();
    if ($vrTourUrl !== null && ! preg_match('/^https?:\/\//i', $vrTourUrl)) {
        $vrTourUrl = 'https://' . $vrTourUrl;
    }
    $contactSettings = ContactSetting::instance();
    $overlayPhone = is_string($contactSettings->phone ?? null) ? trim((string) $contactSettings->phone) : '';

    return view('dha-phase-vr-tour', compact('phase', 'vrTourUrl', 'overlayPhone'));
})->name('dha.phase.vr-tour');

Route::get('/listing/dealers', function () {
    $q = request()->getQueryString();
    $url = route('listing') . ($q !== null && $q !== '' ? '?' . $q : '');

    return redirect()->to($url, 301);
})->name('listing.dealers');

Route::get('/projects', function () {
    $projectTypeSlug = request('project_type');
    $projectType = null;
    if ($projectTypeSlug && is_string($projectTypeSlug)) {
        $projectType = db_safe(
            'projects.project_type_filter',
            fn () => ProjectType::where('slug', $projectTypeSlug)->orWhere('id', $projectTypeSlug)->first()
        );
    }
    $query = db_safe('projects.query', fn () => Project::with('projectTypes')->active()->frontOrdered());
    if (!$query) {
        $projects = collect();
        $pageHeading = 'Our Projects';
        $pageSubheading = 'Browse our featured projects.';
        $cmsPage = db_safe('projects.cms_page', fn () => CmsPage::findBySlug('projects'));
        return view('projects', compact('projects', 'pageHeading', 'pageSubheading', 'cmsPage'));
    }
    if ($projectType) {
        $query->whereHas('projectTypes', function ($q) use ($projectType) {
            $q->where('project_types.id', $projectType->id);
        });
    }
    $projects = db_safe('projects.get', fn () => $query->get(), collect());
    if ($projectType) {
        $pageHeading = $projectType->name . ' Projects';
        $pageSubheading = 'Browse our ' . $projectType->name . ' projects.';
    } else {
        $pageHeading = 'Our Projects';
        $pageSubheading = 'Browse our featured projects.';
    }
    $cmsPage = db_safe('projects.cms_page', fn () => CmsPage::findBySlug('projects'));
    return view('projects', compact('projects', 'pageHeading', 'pageSubheading', 'cmsPage'));
})->name('projects');

Route::get('/contact-us', function () {
    $cmsPage = db_safe('contact.cms_page', fn () => CmsPage::findBySlug('contact-us') ?: CmsPage::findBySlug('contact'));
    $cs = ContactSetting::instance();
    return view('contact', compact('cmsPage', 'cs'));
})->name('contact-us');
Route::post('/contact-us/submit', [ContactMessageController::class, 'store'])->name('contact-us.submit');

Route::get('/sell-or-rent-property', function () {
    $cmsPage = db_safe('sell.cms_page', fn () => CmsPage::findBySlug('sell-or-rent-property'));
    if (! $cmsPage) {
        abort(404);
    }
    $pageSettings = db_safe('sell.page_settings', fn () => SellRentPageSetting::instance());
    if (! $pageSettings) {
        abort(404);
    }
    $dhaPhases = db_safe('sell.dha_phases', fn () => DhaPhase::active()->frontOrdered()->get(['title']), collect());

    return view('sell-property', compact('cmsPage', 'pageSettings', 'dhaPhases'));
})->name('sell-property');
Route::post('/sell-or-rent-property/submit', [SellRentLeadController::class, 'store'])->name('sell-rent-lead.store');

Route::get('/terms-of-use', function () {
    $cmsPage = db_safe('terms.cms_page', fn () => CmsPage::findBySlug('terms-of-use'));
    if (!$cmsPage) {
        abort(404);
    }
    return view('legal-page', compact('cmsPage'));
})->name('terms-of-use');

Route::get('/privacy-policy', function () {
    $cmsPage = db_safe('privacy.cms_page', fn () => CmsPage::findBySlug('privacy-policy'));
    if (!$cmsPage) {
        abort(404);
    }
    return view('legal-page', compact('cmsPage'));
})->name('privacy-policy');

Route::get('/our-team', function () {
    $cmsPage = db_safe('team.cms_page', fn () => CmsPage::findSafe(9));
    $dealers = db_safe('team.dealers', fn () => Dealer::active()
        ->withCount('properties')
        ->orderBy('name')
        ->get(), collect());
    return view('team', compact('cmsPage', 'dealers'));
})->name('team');

Route::get('/our-team/{slug}', function ($slug) {
    $dealer = Dealer::where('slug', $slug)->active()->firstOrFail();
    $dealer->loadCount('properties');
    $properties = $dealer->properties()->with('projectTypes')->active()->frontOrdered()->get();
    $cs = \App\Models\ContactSetting::instance();
    return view('dealer', compact('dealer', 'properties', 'cs'));
})->name('dealer.show');

Route::get('/careers', function () {
    $cmsPage = db_safe('careers.cms_page', fn () => CmsPage::findBySlug('careers'));
    $jobs = db_safe('careers.jobs', fn () => \App\Models\Career::active()->orderByDesc('sort_order')->orderByDesc('id')->get(), collect());
    return view('careers.index', compact('cmsPage', 'jobs'));
})->name('careers.index');

Route::get('/careers/job/{slug}', function ($slug) {
    $career = db_safe('careers.job.find', fn () => \App\Models\Career::where('slug', $slug)->active()->firstOrFail());
    if (!$career) {
        abort(404);
    }
    $cmsPage = db_safe('careers.job.cms_page', fn () => CmsPage::findBySlug('careers'));
    return view('careers.job', compact('career', 'cmsPage'));
})->name('careers.job');

Route::get('/project/vr-tour/{project}', function (Project $project) {
    $project = Project::query()->whereKey($project->id)->active()->firstOrFail();
    $vrTourUrl = is_string($project->vr_tour_url) ? trim($project->vr_tour_url) : '';
    if ($vrTourUrl === '') {
        abort(404);
    }
    if (!preg_match('/^https?:\/\//i', $vrTourUrl)) {
        $vrTourUrl = 'https://' . $vrTourUrl;
    }
    $contactSettings = ContactSetting::instance();
    $overlayPhone = is_string($contactSettings->phone ?? null) ? trim((string) $contactSettings->phone) : '';
    return view('project-vr-tour', compact('project', 'vrTourUrl', 'overlayPhone'));
})->name('project.vr-tour');

Route::post('/careers/job/{slug}/apply', function (Request $request, string $slug) {
    $career = \App\Models\Career::where('slug', $slug)->active()->firstOrFail();
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'mobile' => ['required', 'string', 'max:50'],
        'email' => ['nullable', 'email', 'max:255'],
        'address' => ['nullable', 'string', 'max:500'],
        'city' => ['nullable', 'string', 'max:120'],
        'education' => ['nullable', 'string', 'max:255'],
        'comments' => ['nullable', 'string', 'max:5000'],
        'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
    ]);
    $cvPath = null;
    if ($request->hasFile('cv')) {
        $cvPath = $request->file('cv')->store('job-applications', 'public');
    }
    $app = \App\Models\JobApplication::create([
        'career_id' => $career->id,
        'name' => $validated['name'],
        'mobile' => $validated['mobile'],
        'email' => $validated['email'] ?? null,
        'address' => $validated['address'] ?? null,
        'city' => $validated['city'] ?? null,
        'education' => $validated['education'] ?? null,
        'comments' => $validated['comments'] ?? null,
        'cv_path' => $cvPath,
        'status' => 'new',
    ]);
    \App\Models\AdminNotification::notify(
        \App\Models\AdminNotification::TYPE_JOB_APPLICATION,
        'New job application: ' . $career->title,
        $validated['name'] . ' applied for ' . $career->title,
        route('admin.job-applications.show', $app)
    );
    if (!empty($validated['email'])) {
        try {
            $appName = config('app.name');
            $jobTitle = $career->title;
            $body = "Hello {$validated['name']},\n\nThank you for applying for the position: {$jobTitle}.\n\nWe have received your application and will review it shortly. We will contact you if your profile matches our requirements.\n\nBest regards,\n{$appName}";
            \Illuminate\Support\Facades\Mail::raw($body, function ($message) use ($validated) {
                $message->to($validated['email'])
                    ->subject('Application received – ' . config('app.name'));
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Job application confirmation email failed: ' . $e->getMessage());
        }
    }
    return response()->json(['success' => true, 'message' => 'Your application has been submitted successfully.']);
})->name('careers.apply');

Route::get('/project/{slug}', function ($slug) {
    $project = Project::with('projectTypes')->where('slug', $slug)->active()->firstOrFail();
    $daily = VisitorDailyCount::firstOrCreate(
        ['date' => now()->toDateString()],
        ['count' => 0, 'count_own_listing' => 0, 'count_dealer_listing' => 0, 'count_projects' => 0]
    );
    $daily->increment('count');
    $daily->increment('count_projects');
    $project->increment('view_count');
    return view('project-new', compact('project'));
})->name('project.show');

Route::get('/project-new/{slug}', function ($slug) {
    $project = Project::with('projectTypes')->where('slug', $slug)->active()->firstOrFail();
    return view('project-new', compact('project'));
})->name('project.new.show');

Route::get('/project-old/{slug}', function ($slug) {
    $project = Project::with('projectTypes')->where('slug', $slug)->active()->firstOrFail();
    return view('project', compact('project'));
})->name('project.old.show');

Route::post('/project/request-info', function (Request $request) {
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:50',
        'email' => 'nullable|email|max:255',
        'property_type' => 'nullable|string|max:120',
        'budget' => 'nullable|string|max:120',
        'message' => 'nullable|string|max:5000',
    ]);
    $req = PropertyRequest::create([
        'property_id' => 0,
        'project_id' => (int) $validated['project_id'],
        'type' => 'project',
        'dealer_id' => 0,
        'name' => $validated['name'],
        'phone' => $validated['phone'] ?? null,
        'email' => $validated['email'] ?? null,
        'property_type' => $validated['property_type'] ?? null,
        'budget' => $validated['budget'] ?? null,
        'message' => $validated['message'] ?? null,
    ]);
    \App\Models\AdminNotification::notify(
        \App\Models\AdminNotification::TYPE_PROJECT_REQUEST,
        'New project enquiry',
        $validated['name'] . ' requested info for a project.',
        route('admin.requests.show', $req)
    );
    return response()->json([
        'success' => (bool) $req,
        'message' => 'Your request has been sent successfully. We will get back to you soon.',
    ]);
})->name('project.request-info');

Route::get('/property/{slug}', function ($slug) {
    $property = Property::with(['projectTypes', 'dealer'])
        ->where('slug', $slug)
        ->active()
        ->firstOrFail();
    $daily = VisitorDailyCount::firstOrCreate(
        ['date' => now()->toDateString()],
        ['count' => 0, 'count_own_listing' => 0, 'count_dealer_listing' => 0, 'count_projects' => 0]
    );
    $daily->increment('count');
    if ((int) $property->dealer_id > 0) {
        $daily->increment('count_dealer_listing');
    } else {
        $daily->increment('count_own_listing');
    }
    $property->increment('view_count');
    if ((int) $property->dealer_id > 0) {
        $property->dealer?->increment('view_count');
    }
    $priceFormatted = format_price($property->price_digits, $property->price_string);

    $city = $property->city ? trim((string) $property->city) : '';
    $town = $property->town ? trim((string) $property->town) : '';
    $dealerId = (int) $property->dealer_id;
    $moreProperties = collect();
    $morePropertiesHeading = 'More Properties';

    if ($dealerId > 0) {
        $sameDealer = Property::with(['projectTypes', 'dealer'])
            ->where('dealer_id', $dealerId)
            ->where('id', '!=', $property->id)
            ->active()
            ->inRandomOrder()
            ->limit(6)
            ->get();
        if ($sameDealer->isNotEmpty()) {
            $moreProperties = $sameDealer;
            $morePropertiesHeading = 'More from ' . ($property->dealer ? $property->dealer->name : 'this dealer');
        }
    }
    if ($moreProperties->isEmpty() && $city !== '') {
        $otherDealers = Property::with(['projectTypes', 'dealer'])
            ->where('dealer_id', '!=', 0)
            ->where('id', '!=', $property->id)
            ->where('city', $city)
            ->when($town !== '', fn ($q) => $q->where('town', $town))
            ->whereHas('dealer', fn ($q) => $q->where('status', \App\Models\Dealer::STATUS_ACTIVE))
            ->active()
            ->inRandomOrder()
            ->limit(6)
            ->get();
        if ($otherDealers->isNotEmpty()) {
            $moreProperties = $otherDealers;
            $morePropertiesHeading = $town !== '' ? 'Other properties in ' . $town . ', ' . $city : 'Other properties in ' . $city;
        }
    }
    if ($moreProperties->isEmpty() && $city !== '') {
        $ownSameTown = Property::with(['projectTypes', 'dealer'])
            ->where('dealer_id', 0)
            ->where('id', '!=', $property->id)
            ->where('city', $city)
            ->when($town !== '', fn ($q) => $q->where('town', $town))
            ->active()
            ->inRandomOrder()
            ->limit(6)
            ->get();
        if ($ownSameTown->isNotEmpty()) {
            $moreProperties = $ownSameTown;
            $morePropertiesHeading = $town !== '' ? 'More properties in ' . $town . ', ' . $city : 'More properties in ' . $city;
        }
    }
    if ($moreProperties->isEmpty() && $city !== '') {
        $ownSameCity = Property::with(['projectTypes', 'dealer'])
            ->where('dealer_id', 0)
            ->where('id', '!=', $property->id)
            ->where('city', $city)
            ->active()
            ->inRandomOrder()
            ->limit(6)
            ->get();
        if ($ownSameCity->isNotEmpty()) {
            $moreProperties = $ownSameCity;
            $morePropertiesHeading = 'More properties in ' . $city;
        }
    }

    $morePropertiesData = $moreProperties->map(function ($p) {
        $imageUrl = $p->featured_image
            ? url('storage/' . ltrim($p->featured_image, '/'))
            : asset('theme/images/all/1.jpg');
        $price = format_price($p->price_digits, $p->price_string);
        $purposeLabel = $p->purpose === Property::PURPOSE_RENT ? 'Rent' : ($p->purpose === Property::PURPOSE_SALE ? 'Sale' : (string) ($p->purpose ?? 'Listing'));
        $dealerName = $p->dealer ? $p->dealer->name : '';
        $dealerImageUrl = $p->dealer && $p->dealer->profile_pic
            ? url('storage/' . ltrim($p->dealer->profile_pic, '/'))
            : asset('theme/images/avatar/1.jpg');
        $gallery = is_array($p->gallery) ? $p->gallery : [];
        $photoCount = ($p->featured_image ? 1 : 0) + count($gallery);

        return [
            'id' => $p->id,
            'title' => $p->title,
            'slug' => $p->slug,
            'detail_url' => url('/property/' . $p->slug),
            'featured_image_url' => $imageUrl,
            'price' => $price,
            'purpose_label' => $purposeLabel,
            'property_type' => $p->property_type ?? '',
            'project_type_names' => $p->projectTypes->pluck('name')->values()->all(),
            'short_address' => $p->short_address ?? $p->address ?? '',
            'bedrooms' => $p->bedrooms ?? 0,
            'bathrooms' => $p->bathrooms ?? 0,
            'kitchen' => $p->kitchen ?? 0,
            'dealer_name' => $dealerName,
            'dealer_image_url' => $dealerImageUrl,
            'dealer_url' => dealer_profile_url($p->dealer),
            'photo_count' => $photoCount,
        ];
    })->values()->all();

    return view('property', compact('property', 'priceFormatted', 'morePropertiesData', 'morePropertiesHeading'));
})->name('property.show');

Route::get('/wishlist/panel', function (Request $request) {
    $idsParam = trim((string) $request->query('ids', ''));
    $raw = $request->cookie('etihad_wishlist');
    $ids = [];
    if ($idsParam !== '') {
        $ids = collect(explode(',', $idsParam))
            ->map(fn ($v) => (int) trim((string) $v))
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->values()
            ->all();
    } elseif (is_string($raw) && $raw !== '') {
        $decoded = json_decode(urldecode($raw), true);
        if (is_array($decoded)) {
            $ids = collect($decoded)
                ->map(fn ($v) => (int) $v)
                ->filter(fn ($v) => $v > 0)
                ->unique()
                ->values()
                ->all();
        }
    }

    $properties = collect();
    if (!empty($ids)) {
        $props = Property::query()
            ->select(['id', 'slug', 'title', 'featured_image', 'price_digits', 'price_string'])
            ->whereIn('id', $ids)
            ->active()
            ->get()
            ->keyBy('id');

        $properties = collect($ids)
            ->map(fn ($id) => $props->get($id))
            ->filter();
    }

    return view('partials.wishlist-panel-items', [
        'properties' => $properties,
    ]);
})->name('wishlist.panel');

Route::post('/property/request-showing', function (Request $request) {
    $validated = $request->validate([
        'property_id' => 'required|exists:properties,id',
        'type' => 'required|in:own,dealer',
        'dealer_id' => 'required|integer|min:0',
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:50',
        'email' => 'nullable|email|max:255',
        'message' => 'nullable|string|max:5000',
    ]);
    $req = PropertyRequest::create($validated);
    \App\Models\AdminNotification::notify(
        \App\Models\AdminNotification::TYPE_PROPERTY_REQUEST,
        'New property / showing request',
        $validated['name'] . ' requested a showing.',
        route('admin.requests.show', $req)
    );
    return response()->json(['success' => true, 'message' => 'Your request has been sent successfully. We will get back to you soon.']);
})->name('property.request-showing');

Route::get('/api/listing/address-suggestions', function (Request $request) {
    $q = trim((string) $request->get('q', ''));
    $words = array_values(array_filter(array_map('trim', preg_split('/[\s,]+/', $q, -1, PREG_SPLIT_NO_EMPTY))));
    if (count($words) === 0 || strlen($q) < 2) {
        return response()->json(['suggestions' => []]);
    }
    $query = Property::query()
        ->where('dealer_id', '!=', 0)
        ->whereHas('dealer', function ($q) {
            $q->where('status', Dealer::STATUS_ACTIVE);
        })
        ->active()
        ->select('address', 'short_address', 'city', 'state', 'town');
    foreach ($words as $word) {
        $word = addcslashes($word, '%_\\');
        if ($word === '') {
            continue;
        }
        $term = '%' . $word . '%';
        $query->where(function ($builder) use ($term) {
            $builder->where('address', 'like', $term)
                ->orWhere('short_address', 'like', $term)
                ->orWhere('city', 'like', $term)
                ->orWhere('state', 'like', $term)
                ->orWhere('town', 'like', $term);
        });
    }
    $properties = $query->limit(80)->get();
    $qLower = mb_strtolower($q);
    $wordsLower = array_map('mb_strtolower', $words);
    $scored = [];
    foreach ($properties as $p) {
        $parts = array_filter([
            $p->short_address ?: $p->address,
            $p->town,
            $p->city,
            $p->state,
        ]);
        $label = implode(', ', array_unique($parts));
        if (! $label) {
            continue;
        }
        $score = 0;
        $combined = implode(' ', array_filter([$p->address, $p->short_address, $p->city, $p->state, $p->town]));
        $combinedLower = mb_strtolower($combined);
        if (mb_strpos($combinedLower, $qLower) !== false) {
            $score += 100;
        }
        $fields = array_filter([$p->address, $p->short_address, $p->city, $p->state, $p->town]);
        foreach ($wordsLower as $w) {
            $wordLen = mb_strlen($w);
            foreach ($fields as $f) {
                if ($f === null || $f === '') {
                    continue;
                }
                $fLower = mb_strtolower($f);
                if (mb_strpos($fLower, $w) !== false) {
                    $score += 1;
                }
                if ($wordLen > 0 && mb_substr($fLower, 0, $wordLen) === $w) {
                    $score += 10;
                }
            }
        }
        $scored[] = ['label' => $label, 'value' => $label, 'score' => $score];
    }
    usort($scored, function ($a, $b) {
        return ($b['score'] <=> $a['score']);
    });
    $seen = [];
    $suggestions = [];
    foreach ($scored as $item) {
        if (isset($seen[$item['label']])) {
            continue;
        }
        $seen[$item['label']] = true;
        $suggestions[] = ['label' => $item['label'], 'value' => $item['value']];
        if (count($suggestions) >= 10) {
            break;
        }
    }
    return response()->json(['suggestions' => $suggestions]);
});

Route::get('/api/listing/own', function (Request $request) {
    $query = Property::query()
        ->with('projectTypes:id,name,slug')
        ->where('dealer_id', 0)
        ->active();

    if ($request->filled('address') && is_string($request->address)) {
        $addressInput = trim($request->address);
        $addressWords = array_values(array_filter(array_map('trim', preg_split('/[\s,]+/', $addressInput, -1, PREG_SPLIT_NO_EMPTY))));
        foreach ($addressWords as $word) {
            $word = addcslashes($word, '%_\\');
            if ($word === '') {
                continue;
            }
            $term = '%' . $word . '%';
            $query->where(function ($builder) use ($term) {
                $builder->where('address', 'like', $term)
                    ->orWhere('short_address', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('state', 'like', $term)
                    ->orWhere('town', 'like', $term);
            });
        }
    }

    if ($request->filled('purpose') && in_array($request->purpose, [Property::PURPOSE_SALE, Property::PURPOSE_RENT])) {
        $query->where('purpose', $request->purpose);
    }

    if ($request->filled('project_type') && is_numeric($request->project_type)) {
        $query->whereHas('projectTypes', function ($q) use ($request) {
            $q->where('project_types.id', (int) $request->project_type);
        });
    }

    if ($request->filled('property_type') && in_array($request->property_type, ['plot', 'home', 'plaza', 'flat', 'apartment', 'file'])) {
        $query->where('property_type', $request->property_type);
    }

    if ($request->filled('city') && is_numeric($request->city)) {
        $city = City::find((int) $request->city);
        if ($city) {
            $query->where('city', $city->name);
        }
    }

    if ($request->filled('dha_phase') && is_numeric($request->dha_phase)) {
        $query->where('dha_phase_id', (int) $request->dha_phase);
    }

    $priceMin = $request->input('price_min');
    $priceMax = $request->input('price_max');
    if ($priceMin !== null && $priceMin !== '' && is_numeric($priceMin)) {
        $query->where('price_digits', '>=', (float) $priceMin);
    }
    if ($priceMax !== null && $priceMax !== '' && is_numeric($priceMax)) {
        $query->where('price_digits', '<=', (float) $priceMax);
    }

    if ($request->filled('marla_min') && is_numeric($request->marla_min)) {
        $query->where('area_marla', '>=', (float) $request->marla_min);
    }
    if ($request->filled('marla_max') && is_numeric($request->marla_max)) {
        $query->where('area_marla', '<=', (float) $request->marla_max);
    }

    if ($request->filled('bedrooms') && is_numeric($request->bedrooms)) {
        $query->where('bedrooms', '=', (int) $request->bedrooms);
    }
    if ($request->filled('bathrooms') && is_numeric($request->bathrooms)) {
        $query->where('bathrooms', '=', (int) $request->bathrooms);
    }
    if ($request->filled('kitchen') && is_numeric($request->kitchen)) {
        $query->where('kitchen', '=', (int) $request->kitchen);
    }

    $sort = (string) $request->input('sort', 'latest');
    if ($sort === 'price_asc') {
        $query->orderByRaw('CASE WHEN price_digits IS NULL THEN 1 ELSE 0 END')
            ->orderBy('price_digits', 'asc')
            ->orderBy('sort_order');
    } elseif ($sort === 'price_desc') {
        $query->orderByRaw('CASE WHEN price_digits IS NULL THEN 1 ELSE 0 END')
            ->orderBy('price_digits', 'desc')
            ->orderBy('sort_order');
    } else {
        $query->frontOrdered();
    }

    $properties = $query->get();

    $data = $properties->map(function (Property $p) {
        $imageUrl = $p->featured_image
            ? url('storage/' . ltrim($p->featured_image, '/'))
            : asset('theme/images/all/1.jpg');

        $price = format_price($p->price_digits, $p->price_string);

        $purposeLabel = $p->purpose === Property::PURPOSE_RENT ? 'Rent' : ($p->purpose === Property::PURPOSE_SALE ? 'Sale' : (string) ($p->purpose ?? 'Listing'));

        return [
            'id' => $p->id,
            'slug' => $p->slug,
            'title' => $p->title,
            'description' => $p->description ? \Illuminate\Support\Str::limit(strip_tags($p->description), 160) : '',
            'featured_image_url' => $imageUrl,
            'price' => $price,
            'bedrooms' => $p->bedrooms ?? 0,
            'bathrooms' => $p->bathrooms ?? 0,
            'kitchen' => $p->kitchen ?? 0,
            'short_address' => $p->short_address ?? '',
            'address' => $p->address ?? '',
            'city' => $p->city ?? '',
            'state' => $p->state ?? '',
            'town' => $p->town ?? '',
            'latitude' => $p->latitude,
            'longitude' => $p->longitude,
            'detail_url' => url('/property/' . $p->slug),
            'project_type_names' => $p->projectTypes->pluck('name')->values()->all(),
            'purpose' => $p->purpose ?? '',
            'purpose_label' => $purposeLabel,
        ];
    });

    return response()->json(['properties' => $data, 'count' => $data->count()]);
});

Route::get('/api/listing/dealers', function (Request $request) {
    $query = Property::query()
        ->with(['projectTypes:id,name,slug', 'dealer:id,name,profile_pic,slug'])
        ->where('dealer_id', '!=', 0)
        ->whereHas('dealer', function ($q) {
            $q->where('status', \App\Models\Dealer::STATUS_ACTIVE);
        })
        ->active();

    if ($request->filled('address') && is_string($request->address)) {
        $addressInput = trim($request->address);
        $addressWords = array_values(array_filter(array_map('trim', preg_split('/[\s,]+/', $addressInput, -1, PREG_SPLIT_NO_EMPTY))));
        foreach ($addressWords as $word) {
            $word = addcslashes($word, '%_\\');
            if ($word === '') {
                continue;
            }
            $term = '%' . $word . '%';
            $query->where(function ($builder) use ($term) {
                $builder->where('address', 'like', $term)
                    ->orWhere('short_address', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('state', 'like', $term)
                    ->orWhere('town', 'like', $term);
            });
        }
    }

    if ($request->filled('purpose') && in_array($request->purpose, [Property::PURPOSE_SALE, Property::PURPOSE_RENT])) {
        $query->where('purpose', $request->purpose);
    }

    if ($request->filled('project_type') && is_numeric($request->project_type)) {
        $query->whereHas('projectTypes', function ($q) use ($request) {
            $q->where('project_types.id', (int) $request->project_type);
        });
    }

    if ($request->filled('property_type') && in_array($request->property_type, ['plot', 'home', 'plaza', 'flat', 'apartment', 'file'])) {
        $query->where('property_type', $request->property_type);
    }

    if ($request->filled('city') && is_numeric($request->city)) {
        $city = City::find((int) $request->city);
        if ($city) {
            $query->where('city', $city->name);
        }
    }

    if ($request->filled('dha_phase') && is_numeric($request->dha_phase)) {
        $query->where('dha_phase_id', (int) $request->dha_phase);
    }

    $priceMin = $request->input('price_min');
    $priceMax = $request->input('price_max');
    if ($priceMin !== null && $priceMin !== '' && is_numeric($priceMin)) {
        $query->where('price_digits', '>=', (float) $priceMin);
    }
    if ($priceMax !== null && $priceMax !== '' && is_numeric($priceMax)) {
        $query->where('price_digits', '<=', (float) $priceMax);
    }

    if ($request->filled('marla_min') && is_numeric($request->marla_min)) {
        $query->where('area_marla', '>=', (float) $request->marla_min);
    }
    if ($request->filled('marla_max') && is_numeric($request->marla_max)) {
        $query->where('area_marla', '<=', (float) $request->marla_max);
    }

    if ($request->filled('bedrooms') && is_numeric($request->bedrooms)) {
        $query->where('bedrooms', '=', (int) $request->bedrooms);
    }
    if ($request->filled('bathrooms') && is_numeric($request->bathrooms)) {
        $query->where('bathrooms', '=', (int) $request->bathrooms);
    }
    if ($request->filled('kitchen') && is_numeric($request->kitchen)) {
        $query->where('kitchen', '=', (int) $request->kitchen);
    }

    $sort = (string) $request->input('sort', 'latest');
    if ($sort === 'price_asc') {
        $query->orderByRaw('CASE WHEN price_digits IS NULL THEN 1 ELSE 0 END')
            ->orderBy('price_digits', 'asc')
            ->orderBy('sort_order');
    } elseif ($sort === 'price_desc') {
        $query->orderByRaw('CASE WHEN price_digits IS NULL THEN 1 ELSE 0 END')
            ->orderBy('price_digits', 'desc')
            ->orderBy('sort_order');
    } else {
        $query->frontOrdered();
    }

    $properties = $query->get();

    $data = $properties->map(function (Property $p) {
        $imageUrl = $p->featured_image
            ? url('storage/' . ltrim($p->featured_image, '/'))
            : asset('theme/images/all/1.jpg');

        $price = format_price($p->price_digits, $p->price_string);

        $purposeLabel = $p->purpose === Property::PURPOSE_RENT ? 'Rent' : ($p->purpose === Property::PURPOSE_SALE ? 'Sale' : (string) ($p->purpose ?? 'Listing'));

        $dealerName = $p->dealer ? $p->dealer->name : '';
        $dealerImageUrl = $p->dealer && $p->dealer->profile_pic
            ? url('storage/' . ltrim($p->dealer->profile_pic, '/'))
            : asset('theme/images/avatar/1.jpg');

        return [
            'id' => $p->id,
            'slug' => $p->slug,
            'title' => $p->title,
            'description' => $p->description ? \Illuminate\Support\Str::limit(strip_tags($p->description), 160) : '',
            'featured_image_url' => $imageUrl,
            'price' => $price,
            'bedrooms' => $p->bedrooms ?? 0,
            'bathrooms' => $p->bathrooms ?? 0,
            'kitchen' => $p->kitchen ?? 0,
            'short_address' => $p->short_address ?? '',
            'address' => $p->address ?? '',
            'city' => $p->city ?? '',
            'state' => $p->state ?? '',
            'town' => $p->town ?? '',
            'latitude' => $p->latitude,
            'longitude' => $p->longitude,
            'detail_url' => url('/property/' . $p->slug),
            'project_type_names' => $p->projectTypes->pluck('name')->values()->all(),
            'purpose' => $p->purpose ?? '',
            'purpose_label' => $purposeLabel,
            'dealer_name' => $dealerName,
            'dealer_image_url' => $dealerImageUrl,
            'dealer_url' => dealer_profile_url($p->dealer),
        ];
    });

    return response()->json(['properties' => $data, 'count' => $data->count()]);
});

Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware('admin')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/profile', [AdminProfileController::class, 'show'])->name('admin.profile.show');
    Route::put('/admin/profile', [AdminProfileController::class, 'updateProfile'])->name('admin.profile.update');
    Route::put('/admin/profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.profile.password');

    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/admin/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity_logs.index');

    Route::get('/admin/project-types', [ProjectTypeController::class, 'index'])->name('admin.project_types.index');
    Route::get('/admin/project-types/create', [ProjectTypeController::class, 'create'])->name('admin.project_types.create');
    Route::post('/admin/project-types', [ProjectTypeController::class, 'store'])->name('admin.project_types.store');
    Route::get('/admin/project-types/{projectType}/edit', [ProjectTypeController::class, 'edit'])->name('admin.project_types.edit');
    Route::put('/admin/project-types/{projectType}', [ProjectTypeController::class, 'update'])->name('admin.project_types.update');
    Route::delete('/admin/project-types/{projectType}', [ProjectTypeController::class, 'destroy'])->name('admin.project_types.destroy');

    Route::get('/admin/partners', [PartnerController::class, 'index'])->name('admin.partners.index');
    Route::get('/admin/partners/create', [PartnerController::class, 'create'])->name('admin.partners.create');
    Route::post('/admin/partners', [PartnerController::class, 'store'])->name('admin.partners.store');
    Route::get('/admin/partners/{partner}/edit', [PartnerController::class, 'edit'])->name('admin.partners.edit');
    Route::put('/admin/partners/{partner}', [PartnerController::class, 'update'])->name('admin.partners.update');
    Route::delete('/admin/partners/{partner}', [PartnerController::class, 'destroy'])->name('admin.partners.destroy');

    Route::get('/admin/testimonials', [TestimonialController::class, 'index'])->name('admin.testimonials.index');
    Route::get('/admin/testimonials/create', [TestimonialController::class, 'create'])->name('admin.testimonials.create');
    Route::post('/admin/testimonials', [TestimonialController::class, 'store'])->name('admin.testimonials.store');
    Route::get('/admin/testimonials/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('admin.testimonials.edit');
    Route::put('/admin/testimonials/{testimonial}', [TestimonialController::class, 'update'])->name('admin.testimonials.update');
    Route::delete('/admin/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('admin.testimonials.destroy');

    Route::get('/admin/homepage-hero', [HomepageHeroSettingController::class, 'edit'])->name('admin.homepage-hero.edit');
    Route::put('/admin/homepage-hero', [HomepageHeroSettingController::class, 'update'])->name('admin.homepage-hero.update');
    Route::get('/admin/homepage-vision', [HomepageVisionSettingController::class, 'edit'])->name('admin.homepage-vision.edit');
    Route::put('/admin/homepage-vision', [HomepageVisionSettingController::class, 'update'])->name('admin.homepage-vision.update');
    Route::get('/admin/homepage-why', [HomepageWhySettingController::class, 'edit'])->name('admin.homepage-why.edit');
    Route::put('/admin/homepage-why', [HomepageWhySettingController::class, 'update'])->name('admin.homepage-why.update');
    Route::get('/admin/homepage-about', [HomepageAboutSettingController::class, 'edit'])->name('admin.homepage-about.edit');
    Route::put('/admin/homepage-about', [HomepageAboutSettingController::class, 'update'])->name('admin.homepage-about.update');
    Route::get('/admin/homepage-investment-journey', [HomepageInvestmentJourneyController::class, 'edit'])->name('admin.homepage-investment-journey.edit');
    Route::put('/admin/homepage-investment-journey', [HomepageInvestmentJourneyController::class, 'update'])->name('admin.homepage-investment-journey.update');
    Route::get('/admin/homepage-what-sets-apart', [HomepageWhatSetsApartController::class, 'edit'])->name('admin.homepage-what-sets-apart.edit');
    Route::put('/admin/homepage-what-sets-apart', [HomepageWhatSetsApartController::class, 'update'])->name('admin.homepage-what-sets-apart.update');
    Route::get('/admin/homepage-achievements', [HomepageAchievementsController::class, 'edit'])->name('admin.homepage-achievements.edit');
    Route::put('/admin/homepage-achievements', [HomepageAchievementsController::class, 'update'])->name('admin.homepage-achievements.update');
    Route::get('/admin/homepage-choice', [HomepageChoiceController::class, 'edit'])->name('admin.homepage-choice.edit');
    Route::put('/admin/homepage-choice', [HomepageChoiceController::class, 'update'])->name('admin.homepage-choice.update');
    Route::get('/admin/homepage-dha-section', [HomepageDhaSectionController::class, 'edit'])->name('admin.homepage-dha-section.edit');
    Route::put('/admin/homepage-dha-section', [HomepageDhaSectionController::class, 'update'])->name('admin.homepage-dha-section.update');
    Route::get('/admin/homepage-dealers-section', [HomepageDealersSectionController::class, 'edit'])->name('admin.homepage-dealers-section.edit');
    Route::put('/admin/homepage-dealers-section', [HomepageDealersSectionController::class, 'update'])->name('admin.homepage-dealers-section.update');
    Route::get('/admin/homepage-location-section', [HomepageLocationSectionController::class, 'edit'])->name('admin.homepage-location-section.edit');
    Route::put('/admin/homepage-location-section', [HomepageLocationSectionController::class, 'update'])->name('admin.homepage-location-section.update');
    Route::get('/admin/portal-hero', [PortalHeroSlideController::class, 'index'])->name('admin.portal-hero.index');
    Route::get('/admin/portal-hero/create', [PortalHeroSlideController::class, 'create'])->name('admin.portal-hero.create');
    Route::post('/admin/portal-hero', [PortalHeroSlideController::class, 'store'])->name('admin.portal-hero.store');
    Route::get('/admin/portal-hero/{portal_hero_slide}/edit', [PortalHeroSlideController::class, 'edit'])->name('admin.portal-hero.edit');
    Route::put('/admin/portal-hero/{portal_hero_slide}', [PortalHeroSlideController::class, 'update'])->name('admin.portal-hero.update');
    Route::delete('/admin/portal-hero/{portal_hero_slide}', [PortalHeroSlideController::class, 'destroy'])->name('admin.portal-hero.destroy');
    Route::get('/admin/portal-ads', [PortalAdController::class, 'edit'])->name('admin.portal-ads.edit');
    Route::put('/admin/portal-ads', [PortalAdController::class, 'update'])->name('admin.portal-ads.update');

    Route::get('/admin/sort-order', [ListingSortController::class, 'index'])->name('admin.sort-order.index');
    Route::post('/admin/sort-order', [ListingSortController::class, 'update'])->name('admin.sort-order.update');

    Route::get('/admin/dha', [DhaSettingController::class, 'edit'])->name('admin.dha.edit');
    Route::put('/admin/dha', [DhaSettingController::class, 'update'])->name('admin.dha.update');
    Route::post('/admin/dha/upload-media', [DhaSettingController::class, 'uploadMedia'])->name('admin.dha.upload-media');
    Route::get('/admin/dha-phases', [DhaPhaseController::class, 'index'])->name('admin.dha-phases.index');
    Route::get('/admin/dha-phases/create', [DhaPhaseController::class, 'create'])->name('admin.dha-phases.create');
    Route::post('/admin/dha-phases', [DhaPhaseController::class, 'store'])->name('admin.dha-phases.store');
    Route::post('/admin/dha-phases/upload-media', [DhaPhaseController::class, 'uploadMedia'])->name('admin.dha-phases.upload-media');
    Route::get('/admin/dha-phases/{dhaPhase}/edit', [DhaPhaseController::class, 'edit'])->name('admin.dha-phases.edit');
    Route::put('/admin/dha-phases/{dhaPhase}', [DhaPhaseController::class, 'update'])->name('admin.dha-phases.update');
    Route::delete('/admin/dha-phases/{dhaPhase}', [DhaPhaseController::class, 'destroy'])->name('admin.dha-phases.destroy');

    Route::get('/admin/projects', [ProjectController::class, 'index'])->name('admin.projects.index');
    Route::get('/admin/projects/create', [ProjectController::class, 'create'])->name('admin.projects.create');
    Route::post('/admin/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
    Route::post('/admin/projects/upload-media', [ProjectController::class, 'uploadMedia'])->name('admin.projects.upload-media');
    Route::get('/admin/projects/{project}/preview', [ProjectController::class, 'preview'])->name('admin.projects.preview');
    Route::get('/admin/projects/{project}/edit-section/{section}', [ProjectController::class, 'editSection'])->name('admin.projects.edit-section');
    Route::get('/admin/projects/{project}/edit-section/{section}/load', [ProjectController::class, 'loadSection'])->name('admin.projects.edit-section.load');
    Route::patch('/admin/projects/{project}/sections/{section}', [ProjectController::class, 'updateSection'])->name('admin.projects.sections.update');
    Route::get('/admin/projects/{project}/edit', [ProjectController::class, 'edit'])->name('admin.projects.edit');
    Route::put('/admin/projects/{project}', [ProjectController::class, 'update'])->name('admin.projects.update');
    Route::delete('/admin/projects/{project}', [ProjectController::class, 'destroy'])->name('admin.projects.destroy');
    Route::post('/admin/projects/{project}/duplicate', [ProjectController::class, 'duplicate'])->name('admin.projects.duplicate');

    Route::get('/admin/dealers', [DealerController::class, 'index'])->name('admin.dealers.index');
    Route::get('/admin/dealers/create', [DealerController::class, 'create'])->name('admin.dealers.create');
    Route::post('/admin/dealers', [DealerController::class, 'store'])->name('admin.dealers.store');
    Route::get('/admin/dealers/{dealer}/edit', [DealerController::class, 'edit'])->name('admin.dealers.edit');
    Route::put('/admin/dealers/{dealer}', [DealerController::class, 'update'])->name('admin.dealers.update');
    Route::delete('/admin/dealers/{dealer}', [DealerController::class, 'destroy'])->name('admin.dealers.destroy');

    Route::get('/admin/own-listings', [PropertyController::class, 'indexOwn'])->name('admin.own-listings.index');
    Route::get('/admin/own-listings/create', [PropertyController::class, 'createOwn'])->name('admin.own-listings.create');
    Route::post('/admin/own-listings', [PropertyController::class, 'storeOwn'])->name('admin.own-listings.store');
    Route::post('/admin/own-listings/upload-media', [PropertyController::class, 'uploadMediaOwn'])->name('admin.own-listings.upload-media');
    Route::get('/admin/own-listings/{property}/preview', [PropertyController::class, 'preview'])->name('admin.own-listings.preview');
    Route::get('/admin/own-listings/{property}/edit-section/{section}', [PropertyController::class, 'editSectionOwn'])->name('admin.own-listings.edit-section');
    Route::get('/admin/own-listings/{property}/edit-section/{section}/load', [PropertyController::class, 'loadSectionOwn'])->name('admin.own-listings.edit-section.load');
    Route::patch('/admin/own-listings/{property}/sections/{section}', [PropertyController::class, 'updateSectionOwn'])->name('admin.own-listings.sections.update');
    Route::get('/admin/own-listings/{property}/edit', [PropertyController::class, 'editOwn'])->name('admin.own-listings.edit');
    Route::put('/admin/own-listings/{property}', [PropertyController::class, 'updateOwn'])->name('admin.own-listings.update');
    Route::post('/admin/own-listings/{property}/duplicate', [PropertyController::class, 'duplicateOwn'])->name('admin.own-listings.duplicate');
    Route::delete('/admin/own-listings/{property}', [PropertyController::class, 'destroyOwn'])->name('admin.own-listings.destroy');

    Route::get('/admin/dealer-listings', [PropertyController::class, 'indexDealer'])->name('admin.dealer-listings.index');
    Route::get('/admin/dealer-listings/create', [PropertyController::class, 'createDealer'])->name('admin.dealer-listings.create');
    Route::post('/admin/dealer-listings', [PropertyController::class, 'storeDealer'])->name('admin.dealer-listings.store');
    Route::post('/admin/dealer-listings/upload-media', [PropertyController::class, 'uploadMediaDealer'])->name('admin.dealer-listings.upload-media');
    Route::get('/admin/dealer-listings/{property}/preview', [PropertyController::class, 'preview'])->name('admin.dealer-listings.preview');
    Route::get('/admin/dealer-listings/{property}/edit-section/{section}', [PropertyController::class, 'editSectionDealer'])->name('admin.dealer-listings.edit-section');
    Route::get('/admin/dealer-listings/{property}/edit-section/{section}/load', [PropertyController::class, 'loadSectionDealer'])->name('admin.dealer-listings.edit-section.load');
    Route::patch('/admin/dealer-listings/{property}/sections/{section}', [PropertyController::class, 'updateSectionDealer'])->name('admin.dealer-listings.sections.update');
    Route::get('/admin/dealer-listings/{property}/edit', [PropertyController::class, 'editDealer'])->name('admin.dealer-listings.edit');
    Route::put('/admin/dealer-listings/{property}', [PropertyController::class, 'updateDealer'])->name('admin.dealer-listings.update');
    Route::post('/admin/dealer-listings/{property}/duplicate', [PropertyController::class, 'duplicateDealer'])->name('admin.dealer-listings.duplicate');
    Route::delete('/admin/dealer-listings/{property}', [PropertyController::class, 'destroyDealer'])->name('admin.dealer-listings.destroy');

    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');

    Route::get('/admin/contact-settings', [ContactSettingsController::class, 'edit'])->name('admin.contact-settings.edit');
    Route::put('/admin/contact-settings', [ContactSettingsController::class, 'update'])->name('admin.contact-settings.update');
    Route::get('/admin/site-seo-settings', [SiteSeoSettingsController::class, 'edit'])->name('admin.site-seo-settings.edit');
    Route::put('/admin/site-seo-settings', [SiteSeoSettingsController::class, 'update'])->name('admin.site-seo-settings.update');
    Route::get('/admin/contact-messages', [ContactMessageController::class, 'index'])->name('admin.contact-messages.index');
    Route::get('/admin/contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('admin.contact-messages.show');
    Route::put('/admin/contact-messages/{contactMessage}/status', [ContactMessageController::class, 'updateStatus'])->name('admin.contact-messages.update-status');

    Route::get('/admin/requests/projects', [PropertyRequestController::class, 'indexProjects'])->name('admin.requests.projects');
    Route::get('/admin/requests/properties', [PropertyRequestController::class, 'indexProperties'])->name('admin.requests.properties');
    Route::get('/admin/requests/{propertyRequest}', [PropertyRequestController::class, 'show'])->name('admin.requests.show');

    Route::get('/admin/cms-pages', [CmsPageController::class, 'index'])->name('admin.cms-pages.index');
    Route::get('/admin/cms-pages/{cmsPage}/edit', [CmsPageController::class, 'edit'])->name('admin.cms-pages.edit');
    Route::put('/admin/cms-pages/{cmsPage}', [CmsPageController::class, 'update'])->name('admin.cms-pages.update');

    Route::get('/admin/careers', [CareerController::class, 'index'])->name('admin.careers.index');
    Route::get('/admin/careers/create', [CareerController::class, 'create'])->name('admin.careers.create');
    Route::post('/admin/careers', [CareerController::class, 'store'])->name('admin.careers.store');
    Route::get('/admin/careers/{career}/edit', [CareerController::class, 'edit'])->name('admin.careers.edit');
    Route::put('/admin/careers/{career}', [CareerController::class, 'update'])->name('admin.careers.update');
    Route::delete('/admin/careers/{career}', [CareerController::class, 'destroy'])->name('admin.careers.destroy');

    Route::get('/admin/job-applications', [JobApplicationController::class, 'index'])->name('admin.job-applications.index');
    Route::get('/admin/job-applications/{jobApplication}', [JobApplicationController::class, 'show'])->name('admin.job-applications.show');
    Route::put('/admin/job-applications/{jobApplication}/status', [JobApplicationController::class, 'updateStatus'])->name('admin.job-applications.update-status');

    Route::get('/admin/notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/admin/notifications/{notification}/read', [AdminNotificationController::class, 'markRead'])->name('admin.notifications.read');
    Route::post('/admin/notifications/mark-all-read', [AdminNotificationController::class, 'markAllRead'])->name('admin.notifications.mark-all-read');
});
