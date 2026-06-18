<?php

/**
 * Full default content for DHA Lahore phases (1–11).
 * Used by migration 2026_06_08_150000_seed_dha_phases_full_content.
 */

$sharedValuePropositions = [
    ['icon' => 'map-pin', 'title' => 'High Demand', 'text' => 'Most sought-after location in DHA'],
    ['icon' => 'shield-check', 'title' => 'Secure Investment', 'text' => 'Stable growth and high ROI'],
    ['icon' => 'gem', 'title' => 'Premium Lifestyle', 'text' => 'World class amenities and living'],
    ['icon' => 'navigation', 'title' => 'Excellent Connectivity', 'text' => 'Easy access to all major areas of Lahore'],
];

$sharedAttractions = [
    ['icon' => 'map-pin', 'title' => 'Prime & Central Location', 'text' => 'Easy access to all major destinations in Lahore', 'image' => ''],
    ['icon' => 'trees', 'title' => 'Parks & Green Spaces', 'text' => 'Lush green parks and open areas', 'image' => ''],
    ['icon' => 'building-2', 'title' => 'Mosques', 'text' => 'Beautiful mosques within the community', 'image' => ''],
    ['icon' => 'store', 'title' => 'Commercial Hubs', 'text' => 'Nearby markets, malls & business centers', 'image' => ''],
    ['icon' => 'graduation-cap', 'title' => 'Top Educational Institutions', 'text' => 'Reputed schools & colleges in close proximity', 'image' => ''],
    ['icon' => 'shield', 'title' => 'Secure & Gated Community', 'text' => '24/7 security for a safe and peaceful living', 'image' => ''],
];

$sharedInvestmentReasons = [
    ['icon' => 'map-pin', 'title' => 'Prime & Central Location', 'text' => 'Strategically located in the heart of Lahore'],
    ['icon' => 'trending-up', 'title' => 'High Rental Yield', 'text' => 'Excellent rental income potential'],
    ['icon' => 'layout-grid', 'title' => 'Developed Infrastructure', 'text' => 'Fully developed with modern utilities'],
    ['icon' => 'line-chart', 'title' => 'Strong Future Growth', 'text' => 'Consistent price appreciation over the years'],
    ['icon' => 'leaf', 'title' => 'Safe & Peaceful Environment', 'text' => 'Clean, green and secure surroundings'],
    ['icon' => 'users', 'title' => 'Trusted & Established Community', 'text' => 'A well-planned and established neighborhood'],
];

$stats = [
    1 => ['area' => '5,987 Kanal', 'plots' => '54,541+', 'year' => '2002', 'lat' => '31.47672300', 'lng' => '74.38408700', 'location' => 'Canal Bank Road, DHA Phase 1, Lahore'],
    2 => ['area' => '6,200 Kanal', 'plots' => '48,200+', 'year' => '2005', 'lat' => '31.46789100', 'lng' => '74.39123400', 'location' => 'Ghazi Road, DHA Phase 2, Lahore'],
    3 => ['area' => '5,450 Kanal', 'plots' => '42,800+', 'year' => '2008', 'lat' => '31.45412300', 'lng' => '74.40345600', 'location' => 'Defence Road, DHA Phase 3, Lahore'],
    4 => ['area' => '4,980 Kanal', 'plots' => '38,500+', 'year' => '2011', 'lat' => '31.44123400', 'lng' => '74.41567800', 'location' => 'DHA Phase 4, Lahore'],
    5 => ['area' => '4,650 Kanal', 'plots' => '35,200+', 'year' => '2014', 'lat' => '31.42834500', 'lng' => '74.42789000', 'location' => 'DHA Phase 5, Lahore'],
    6 => ['area' => '4,200 Kanal', 'plots' => '31,600+', 'year' => '2016', 'lat' => '31.41545600', 'lng' => '74.44012300', 'location' => 'DHA Phase 6, Lahore'],
    7 => ['area' => '3,850 Kanal', 'plots' => '28,400+', 'year' => '2018', 'lat' => '31.40256700', 'lng' => '74.45234500', 'location' => 'DHA Phase 7, Lahore'],
    8 => ['area' => '3,500 Kanal', 'plots' => '25,100+', 'year' => '2020', 'lat' => '31.38967800', 'lng' => '74.46456700', 'location' => 'DHA Phase 8, Lahore'],
    9 => ['area' => '3,200 Kanal', 'plots' => '22,800+', 'year' => '2021', 'lat' => '31.37678900', 'lng' => '74.47678900', 'location' => 'DHA Phase 9, Lahore'],
    10 => ['area' => '2,900 Kanal', 'plots' => '19,500+', 'year' => '2022', 'lat' => '31.36389000', 'lng' => '74.48890100', 'location' => 'DHA Phase 10, Lahore'],
    11 => ['area' => '2,600 Kanal', 'plots' => '16,200+', 'year' => '2023', 'lat' => '31.35090100', 'lng' => '74.50112300', 'location' => 'DHA Phase 11, Lahore'],
];

$heroLeads = [
    1 => 'The flagship DHA community with canal-side premium living, mature infrastructure, and exceptional investment potential.',
    2 => 'A well-established phase known for strong residential demand, wide boulevards, and excellent connectivity across Lahore.',
    3 => 'Popular with families and investors alike — modern blocks, green belts, and a vibrant commercial corridor.',
    4 => 'Balanced mix of residential and commercial inventory with steady appreciation and high end-user interest.',
    5 => 'Contemporary urban planning with premium amenities, schools nearby, and strong rental yields.',
    6 => 'Growing phase with modern utilities, landscaped sectors, and increasing commercial activity.',
    7 => 'Strategic location with developing infrastructure and attractive entry prices for long-term investors.',
    8 => 'Newer development offering smart community design, wide roads, and future growth upside.',
    9 => 'Emerging premium sector with planned commercial hubs and family-oriented residential blocks.',
    10 => 'Latest expansion with contemporary plot sizes, underground utilities, and modern security.',
    11 => 'The newest DHA phase — ideal for early investors seeking appreciation in a master-planned community.',
];

$phases = [];

for ($n = 1; $n <= 11; $n++) {
    $title = 'DHA Phase ' . $n;
    $slug = 'dha-phase-' . $n;
    $s = $stats[$n];
    $lead = $heroLeads[$n];

    $phases[] = [
        'title' => $title,
        'slug' => $slug,
        'sort_order' => $n,
        'description' => '<p><strong>' . $title . '</strong> is one of the most desirable sectors within Defence Housing Authority Lahore, offering planned residential and commercial opportunities with world-class infrastructure.</p>'
            . '<p>Residents enjoy gated security, underground electrification, fiber connectivity, parks, mosques, schools, and direct access to major city arteries. ' . $title . ' remains a top choice for end-users and investors seeking stable growth in Lahore\'s premium real estate market.</p>',
        'hero_lead' => $lead,
        'stat_location' => 'Lahore, Pakistan',
        'stat_total_area' => $s['area'],
        'stat_total_plots' => $s['plots'],
        'stat_year_developed' => $s['year'],
        'features_content' => '<p>' . $title . ' offers planned residential and commercial blocks, wide roads, underground utilities, parks, and community facilities designed for modern living.</p>'
            . '<ul><li>Gated community with 24/7 security</li><li>Underground electrification &amp; fiber connectivity</li><li>Parks, mosques, and commercial boulevards</li><li>Direct access to major city arteries</li><li>Verified dealer listings for plots, homes &amp; commercial units</li></ul>',
        'market_insights' => '<p>Property values in ' . $title . ' have shown steady appreciation driven by infrastructure completion, commercial activity, and sustained buyer demand from end-users and investors.</p>'
            . '<p>Average plot and home prices remain competitive versus comparable premium societies in Lahore, with strong rental yield in established sectors. Early-phase inventory in newer blocks continues to attract long-term capital.</p>',
        'contact_intro' => 'Speak with our DHA specialists for availability, pricing, and verified listings in ' . $title . '.',
        'value_propositions' => $sharedValuePropositions,
        'attractions_heading' => 'ATTRACTIONS NEAR ' . strtoupper($title),
        'attractions' => $sharedAttractions,
        'investment_reasons' => $sharedInvestmentReasons,
        'project_highlights' => [
            'tag_primary' => $n <= 3 ? 'Residential & Commercial' : 'Residential',
            'tag_secondary' => 'Sale',
            'location' => $s['location'],
            'total_views' => $s['plots'],
            'developed_year' => $s['year'],
            'register_title' => 'Register Interest',
            'register_text' => 'Get updates and alerts about listings in ' . $title . '.',
            'register_url' => '#dha-contact',
        ],
        'help_bar_eyebrow' => 'HAVE QUESTIONS?',
        'help_bar_title' => "We're Here to Help!",
        'help_bar_text' => 'Connect with our property experts for more details about ' . $title . ', plot maps, and verified dealer listings.',
        'latitude' => $s['lat'],
        'longitude' => $s['lng'],
        'map_zoom' => 14,
        'meta_title' => $title . ' Lahore | Etihad Marketing',
        'meta_description' => 'Explore ' . $title . ' Lahore — maps, plot plans, galleries, property types, and verified dealer listings.',
        'meta_keywords' => 'DHA Lahore, ' . $title . ', plots, houses, commercial, Etihad Marketing',
        'status' => 'active',
    ];
}

return $phases;
