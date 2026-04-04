<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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
use App\Http\Controllers\PortalHeroSlideController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ContactSettingsController;
use App\Http\Controllers\PropertyRequestController;
use App\Http\Controllers\CmsPageController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\AdminNotificationController;
use App\Models\PropertyRequest;
use App\Models\PortalHeroSlide;
use App\Models\Partner;
use App\Models\Testimonial;
use App\Models\Project;
use App\Models\VisitorDailyCount;
use App\Models\CmsPage;
use App\Models\Dealer;

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
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();
        return response()->json([
            'success' => true,
            'message' => 'Migrations completed.',
            'output' => $output,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'output' => $e->getTraceAsString(),
        ], 500);
    }
})->name('run-migrations');

Route::get('/', function () {
    $cmsPage = CmsPage::findBySlug('home');
    return view('index', compact('cmsPage'));
});

Route::get('/portal', function () {
    $projectTypes = ProjectType::orderBy('name')->get(['id', 'name', 'slug']);
    $lahoreCityId = City::whereRaw('LOWER(name) = ?', ['lahore'])->value('id');

    $hotPropertyCards = Property::query()
        ->with(['projectTypes:id,name', 'dealer:id,name,profile_pic'])
        ->where('is_hot', true)
        ->where('dealer_id', '!=', 0)
        ->whereHas('dealer', function ($q) {
            $q->where('status', Dealer::STATUS_ACTIVE);
        })
        ->active()
        ->orderByDesc('id')
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
                'excerpt' => $p->description ? \Illuminate\Support\Str::limit(strip_tags($p->description), 120) : '',
                'purpose' => $p->purpose,
                'filter_class' => $filterClass,
            ];
        });

    $portalCarouselProjects = Project::query()
        ->active()
        ->whereNotNull('homepage_listing_image')
        ->where('homepage_listing_image', '!=', '')
        ->orderByDesc('id')
        ->limit(12)
        ->get(['id', 'title', 'slug', 'city', 'homepage_listing_image']);

    $portalPartners = Partner::query()
        ->whereNotNull('image')
        ->where('image', '!=', '')
        ->orderBy('id')
        ->get(['id', 'title', 'image']);

    $portalTestimonials = Testimonial::query()
        ->whereNotNull('image')
        ->where('image', '!=', '')
        ->whereNotNull('comment')
        ->where('comment', '!=', '')
        ->orderBy('id')
        ->get(['id', 'name', 'image', 'comment', 'city']);

    $portalHeroSlides = PortalHeroSlide::query()
        ->active()
        ->whereNotNull('image')
        ->where('image', '!=', '')
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get(['id', 'image']);

    return view('portal', compact('projectTypes', 'lahoreCityId', 'hotPropertyCards', 'portalCarouselProjects', 'portalPartners', 'portalTestimonials', 'portalHeroSlides'));
})->name('portal');

Route::get('/listing', function () {
    $projectTypes = ProjectType::orderBy('name')->get(['id', 'name', 'slug']);
    $cmsPage = CmsPage::findBySlug('listing');
    $lahoreCityId = City::whereRaw('LOWER(name) = ?', ['lahore'])->value('id');
    // Front: “Listing” URL shows dealer listings only (own listings stay admin-only).
    return view('listing', ['projectTypes' => $projectTypes, 'cmsPage' => $cmsPage, 'lahoreCityId' => $lahoreCityId]);
})->name('listing');

Route::get('/listing/dealers', function () {
    $q = request()->getQueryString();
    $url = route('listing') . ($q !== null && $q !== '' ? '?' . $q : '');

    return redirect()->to($url, 301);
})->name('listing.dealers');

Route::get('/projects', function () {
    $projectTypeSlug = request('project_type');
    $projectType = null;
    if ($projectTypeSlug && is_string($projectTypeSlug)) {
        $projectType = ProjectType::where('slug', $projectTypeSlug)->orWhere('id', $projectTypeSlug)->first();
    }
    $query = Project::with('projectTypes')->active()->orderByDesc('id');
    if ($projectType) {
        $query->whereHas('projectTypes', function ($q) use ($projectType) {
            $q->where('project_types.id', $projectType->id);
        });
    }
    $projects = $query->get();
    if ($projectType) {
        $pageHeading = $projectType->name . ' Projects';
        $pageSubheading = 'Browse our ' . $projectType->name . ' projects.';
    } else {
        $pageHeading = 'Our Projects';
        $pageSubheading = 'Browse our featured projects.';
    }
    $cmsPage = CmsPage::findBySlug('projects');
    return view('projects', compact('projects', 'pageHeading', 'pageSubheading', 'cmsPage'));
})->name('projects');

Route::get('/our-team', function () {
    $cmsPage = CmsPage::find(9);
    $dealers = Dealer::active()
        ->withCount('properties')
        ->orderBy('name')
        ->get();
    return view('team', compact('cmsPage', 'dealers'));
})->name('team');

Route::get('/our-team/{slug}', function ($slug) {
    $dealer = Dealer::where('slug', $slug)->active()->firstOrFail();
    $dealer->loadCount('properties');
    $properties = $dealer->properties()->with('projectTypes')->active()->orderByDesc('id')->get();
    $cs = \App\Models\ContactSetting::instance();
    return view('dealer', compact('dealer', 'properties', 'cs'));
})->name('dealer.show');

Route::get('/careers', function () {
    $cmsPage = CmsPage::findBySlug('careers');
    $jobs = \App\Models\Career::active()->orderByDesc('sort_order')->orderByDesc('id')->get();
    return view('careers.index', compact('cmsPage', 'jobs'));
})->name('careers.index');

Route::get('/careers/job/{slug}', function ($slug) {
    $career = \App\Models\Career::where('slug', $slug)->active()->firstOrFail();
    $cmsPage = CmsPage::findBySlug('careers');
    return view('careers.job', compact('career', 'cmsPage'));
})->name('careers.job');

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
    return view('project', compact('project'));
})->name('project.show');

Route::post('/project/request-info', function (Request $request) {
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:50',
        'email' => 'nullable|email|max:255',
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
        ->active()
        ->orderByDesc('id');

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
            ->orderByDesc('id');
    } elseif ($sort === 'price_desc') {
        $query->orderByRaw('CASE WHEN price_digits IS NULL THEN 1 ELSE 0 END')
            ->orderBy('price_digits', 'desc')
            ->orderByDesc('id');
    } else {
        $query->orderByDesc('id');
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
        ->with(['projectTypes:id,name,slug', 'dealer:id,name,profile_pic'])
        ->where('dealer_id', '!=', 0)
        ->whereHas('dealer', function ($q) {
            $q->where('status', \App\Models\Dealer::STATUS_ACTIVE);
        })
        ->active()
        ->orderByDesc('id');

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
            ->orderByDesc('id');
    } elseif ($sort === 'price_desc') {
        $query->orderByRaw('CASE WHEN price_digits IS NULL THEN 1 ELSE 0 END')
            ->orderBy('price_digits', 'desc')
            ->orderByDesc('id');
    } else {
        $query->orderByDesc('id');
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

    Route::get('/admin/portal-hero', [PortalHeroSlideController::class, 'index'])->name('admin.portal-hero.index');
    Route::get('/admin/portal-hero/create', [PortalHeroSlideController::class, 'create'])->name('admin.portal-hero.create');
    Route::post('/admin/portal-hero', [PortalHeroSlideController::class, 'store'])->name('admin.portal-hero.store');
    Route::get('/admin/portal-hero/{portal_hero_slide}/edit', [PortalHeroSlideController::class, 'edit'])->name('admin.portal-hero.edit');
    Route::put('/admin/portal-hero/{portal_hero_slide}', [PortalHeroSlideController::class, 'update'])->name('admin.portal-hero.update');
    Route::delete('/admin/portal-hero/{portal_hero_slide}', [PortalHeroSlideController::class, 'destroy'])->name('admin.portal-hero.destroy');

    Route::get('/admin/projects', [ProjectController::class, 'index'])->name('admin.projects.index');
    Route::get('/admin/projects/create', [ProjectController::class, 'create'])->name('admin.projects.create');
    Route::post('/admin/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
    Route::get('/admin/projects/{project}/preview', [ProjectController::class, 'preview'])->name('admin.projects.preview');
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
    Route::get('/admin/own-listings/{property}/preview', [PropertyController::class, 'preview'])->name('admin.own-listings.preview');
    Route::get('/admin/own-listings/{property}/edit', [PropertyController::class, 'editOwn'])->name('admin.own-listings.edit');
    Route::put('/admin/own-listings/{property}', [PropertyController::class, 'updateOwn'])->name('admin.own-listings.update');
    Route::post('/admin/own-listings/{property}/duplicate', [PropertyController::class, 'duplicateOwn'])->name('admin.own-listings.duplicate');
    Route::delete('/admin/own-listings/{property}', [PropertyController::class, 'destroyOwn'])->name('admin.own-listings.destroy');

    Route::get('/admin/dealer-listings', [PropertyController::class, 'indexDealer'])->name('admin.dealer-listings.index');
    Route::get('/admin/dealer-listings/create', [PropertyController::class, 'createDealer'])->name('admin.dealer-listings.create');
    Route::post('/admin/dealer-listings', [PropertyController::class, 'storeDealer'])->name('admin.dealer-listings.store');
    Route::get('/admin/dealer-listings/{property}/preview', [PropertyController::class, 'preview'])->name('admin.dealer-listings.preview');
    Route::get('/admin/dealer-listings/{property}/edit', [PropertyController::class, 'editDealer'])->name('admin.dealer-listings.edit');
    Route::put('/admin/dealer-listings/{property}', [PropertyController::class, 'updateDealer'])->name('admin.dealer-listings.update');
    Route::post('/admin/dealer-listings/{property}/duplicate', [PropertyController::class, 'duplicateDealer'])->name('admin.dealer-listings.duplicate');
    Route::delete('/admin/dealer-listings/{property}', [PropertyController::class, 'destroyDealer'])->name('admin.dealer-listings.destroy');

    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');

    Route::get('/admin/contact-settings', [ContactSettingsController::class, 'edit'])->name('admin.contact-settings.edit');
    Route::put('/admin/contact-settings', [ContactSettingsController::class, 'update'])->name('admin.contact-settings.update');

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
