<?php

$html = file_get_contents(__DIR__ . '/../homepage.html');

$replacement = <<<'JS'
        const homepageMapProperties = __HOMEPAGE_MAP_PROPERTIES_JSON__;
        const markerColor = '#bb9c46';
        const allLocations = (homepageMapProperties || []).map((property) => ({
          id: property.id,
          name: property.title,
          phase: property.badge || property.purpose_label || 'Listing',
          lat: Number(property.latitude),
          lng: Number(property.longitude),
          description: property.description || property.address || '',
          size: property.size || property.price || '—',
          status: property.status || property.purpose_label || 'Available',
          url: property.detail_url || '#',
        })).filter((location) => location.lat && location.lng);
JS;

$html = preg_replace(
    '/\/\/ DHA Lahore Developments[\s\S]*?const allLocations = \[\];\s*developments\.forEach\(\(dev\) => \{\s*allLocations\.push\(\.\.\.dev\.locations\);\s*\}\);/s',
    trim($replacement),
    $html,
    1,
    $count
);

if ($count !== 1) {
    fwrite(STDERR, "Failed to replace map data block ({$count}).\n");
    exit(1);
}

$html = preg_replace(
    '/\s*\/\/ Initialize Google Map\s*\/\/ Populate sidebar with communities and locations\s*function populateSidebar\(\) \{[\s\S]*?\}\s*/s',
    "\n\n        ",
    $html,
    1,
    $sidebarCount
);

if ($sidebarCount !== 1) {
    fwrite(STDERR, "Failed to remove populateSidebar ({$sidebarCount}).\n");
    exit(1);
}

$html = str_replace(
    "const community = developments.find(d => d.id === location.community);\n            const markerColor = community ? community.color : '#bb9c46';",
    'const markerFillColor = markerColor;',
    $html
);

$html = str_replace('fillColor: markerColor,', 'fillColor: markerFillColor,', $html);
$html = str_replace('style="border-color: ${markerColor}; color: ${markerColor};"', 'style="border-color: ${markerFillColor}; color: ${markerFillColor};"', $html);
$html = str_replace(
    '<button style="background: linear-gradient(135deg, ${markerColor} 0%, ${markerColor}dd 100%);" onclick="alert(\'Contact us for more details about ${location.name}\')">View Details</button>',
    '<a href="${location.url}" style="display:inline-flex;align-items:center;justify-content:center;padding:1rem 1.6rem;border-radius:999px;background:linear-gradient(135deg, ${markerFillColor} 0%, ${markerFillColor}dd 100%);color:#0a0a0a;font-weight:700;text-decoration:none;">View listing</a>',
    $html
);

$html = preg_replace(
    '/(infoWindows\.push\(infoWindow\);\s*\n\s*\/\/ Click event for marker[\s\S]*?\}\);\s*\n\s*\}\);)/',
    '$1' . "\n\n          if (allLocations.length) {\n            const bounds = new google.maps.LatLngBounds();\n            allLocations.forEach((location) => bounds.extend({ lat: location.lat, lng: location.lng }));\n            map.fitBounds(bounds, 80);\n          }",
    $html,
    1,
    $fitCount
);

$html = str_replace(
    "populateSidebar();\n            initializeGoogleMap();",
    'initializeGoogleMap();',
    $html
);

$html = str_replace(
    "populateSidebar();\n          initializeGoogleMap();",
    'initializeGoogleMap();',
    $html
);

file_put_contents(__DIR__ . '/../homepage.html', $html);
echo "Homepage map section patched for dynamic properties.\n";
