<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$p = \App\Models\DhaPhase::where('slug', 'dha-phase-1')->first();
if (!$p) {
    echo "NO_PHASE\n";
    exit(1);
}
$html = view('dha-phase', [
    'phase' => $p,
    'projectTypes' => \App\Models\ProjectType::limit(3)->get(),
    'dhaPhases' => \App\Models\DhaPhase::limit(3)->get(),
    'lahoreCityId' => 1,
    'dhaPhaseUrls' => [],
])->render();
$emu = strpos($html, 'height-emulator');
$wrapper = strpos($html, 'class="wrapper"');
$closeWrapper = strrpos($html, 'class="wrapper"');
// Footer should appear before last closing wrapper divs
$footer = strpos($html, 'main-footer');
echo "emu=$emu footer=$footer wrapper=$wrapper\n";
echo (strpos($html, 'dha-phase-listings') !== false ? "HAS_LISTINGS\n" : "NO_LISTINGS\n");
// Simple check: height-emulator should come after dha-phase-listings
$listings = strpos($html, 'dha-phase-listings');
echo ($listings !== false && $emu !== false && $emu > $listings ? "FOOTER_AFTER_CONTENT\n" : "ORDER_ISSUE\n");
