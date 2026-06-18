<?php

$path = dirname(__DIR__) . '/homepage.html';
$html = file_get_contents($path);

function replace_between(string $html, string $start, string $end, string $replacement): string
{
    $posStart = strpos($html, $start);
    if ($posStart === false) {
        fwrite(STDERR, "Start marker not found: {$start}\n");
        exit(1);
    }
    $contentStart = $posStart + strlen($start);
    $posEnd = strpos($html, $end, $contentStart);
    if ($posEnd === false) {
        fwrite(STDERR, "End marker not found after: {$start}\n");
        exit(1);
    }

    return substr($html, 0, $contentStart) . $replacement . substr($html, $posEnd);
}

$html = replace_between($html, "    <title>", "    <!-- favicon  -->", "\n__HOMEPAGE_HEAD_SEO__\n\n    ");
$html = str_replace(
    "    <link rel=\"stylesheet\" href=\"assets/homepage-DUfNMqJf.css\" />\n    <script type=\"module\" defer src=\"assets/homepage-D6GbVUca.js\"></script>",
    "    <link rel=\"stylesheet\" href=\"assets/homepage-DUfNMqJf.css\" />\n    <script type=\"module\" defer src=\"assets/homepage-D6GbVUca.js\"></script>\n\n__HOMEPAGE_TRACKING_HEAD__",
    $html
);

$html = str_replace(
    '<body data-scrolling-started="false" data-scrolling-direction="up">',
    "<body data-scrolling-started=\"false\" data-scrolling-direction=\"up\">\n__HOMEPAGE_TRACKING_BODY_OPEN__",
    $html
);

$html = str_replace('</body>', "__HOMEPAGE_TRACKING_BODY_CLOSE__\n  </body>", $html);

$html = replace_between(
    $html,
    '                <div class="menu-socials__container">',
    '              <a class="CTA-btn" href="javascript:void(0);">',
    "\n__HOMEPAGE_MENU_CONTACT__\n\n              "
);

$html = replace_between(
    $html,
    '          <div class="location-card">',
    '        </section>',
    "\n__HOMEPAGE_LOCATION_CARD__\n        "
);

$html = replace_between(
    $html,
    '          <!-- contact  -->',
    '          <!-- socials  -->',
    "\n__HOMEPAGE_FOOTER_CONTACT__\n\n          "
);

$html = replace_between(
    $html,
    '          <!-- socials  -->',
    '          <!-- image  -->',
    "\n__HOMEPAGE_FOOTER_SOCIALS__\n\n          "
);

file_put_contents($path, $html);
echo "Patched homepage contact/seo placeholders\n";
