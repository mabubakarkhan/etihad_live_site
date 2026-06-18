<?php

/**
 * Default content for sell / rent property landing page.
 * Used by migration 2026_06_11_100000_create_sell_rent_page_settings_table.php
 */
return [
    'hero_image' => '',
    'hero_bubbles' => [
        ['label' => 'Selling for', 'value' => 'PKR 3M', 'position' => 1],
        ['label' => 'Renting for', 'value' => 'PKR 95K/year', 'position' => 2],
        ['label' => 'Selling for', 'value' => 'PKR 5M', 'position' => 3],
    ],
    'valuation_heading' => 'Start with the right price. Instant valuation insights for DHA',
    'valuation_price' => 'PKR 3,630,000',
    'valuation_badge' => 'High Confidence',
    'valuation_meta' => [
        ['label' => 'Last 6 months change:', 'value' => '+8%', 'highlight' => true],
        ['label' => 'Estimate range:', 'value' => 'PKR 3.0M – PKR 4.6M', 'highlight' => false],
        ['label' => 'Price per sqft:', 'value' => 'PKR 1,444', 'highlight' => false],
    ],
    'valuation_chart_image' => '',
    'valuation_copy' => 'Get a quick, data-driven estimate of your property\'s current value, plus rent and market trends in DHA Lahore, to help you decide the best price to sell or rent.',
    'transactions_heading' => 'See what buyers are really paying with DHA Transactions',
    'transaction_stats' => [
        ['label' => 'Sales Volume', 'value' => '5,779', 'change' => '+12%', 'is_up' => true],
        ['label' => 'Sales Value (PKR)', 'value' => '15.6 B', 'change' => '+9%', 'is_up' => true],
        ['label' => 'Average Price (PKR)', 'value' => '2,714/sqft', 'change' => '+6%', 'is_up' => true],
    ],
    'transactions' => [
        ['date' => '12 Jun 2025', 'location' => 'DHA Phase 6, Lahore', 'price' => '3,200,000', 'type' => 'Plot'],
        ['date' => '10 Jun 2025', 'location' => 'DHA Phase 5, Lahore', 'price' => '8,500,000', 'type' => 'Villa'],
        ['date' => '08 Jun 2025', 'location' => 'DHA Phase 2, Lahore', 'price' => '2,150,000', 'type' => 'Apartment'],
        ['date' => '05 Jun 2025', 'location' => 'DHA Phase 8, Lahore', 'price' => '4,750,000', 'type' => 'Home'],
        ['date' => '02 Jun 2025', 'location' => 'DHA Phase 1, Lahore', 'price' => '12,000,000', 'type' => 'Commercial'],
    ],
    'transactions_copy' => 'Browse real, up-to-date sales in your DHA phase or community to benchmark your price with actual market activity.',
    'faqs_heading' => 'Frequently Asked Questions',
    'faqs' => [
        [
            'question' => 'How do I list my property in DHA Lahore?',
            'answer' => 'Fill out the form on this page with your property details. Our team will contact you to verify information, arrange photography if needed, and publish your listing across our network.',
        ],
        [
            'question' => 'What documents are required to sell or rent?',
            'answer' => 'Typically you will need ownership documents, CNIC, and recent utility bills. For rental listings, a tenancy agreement template can be prepared with our support.',
        ],
        [
            'question' => 'How long does it take to find a buyer or tenant?',
            'answer' => 'Timelines vary by location, price, and property type. DHA Lahore listings often receive enquiries within the first few weeks when priced competitively.',
        ],
        [
            'question' => 'Do you help with property valuation?',
            'answer' => 'Yes. We provide data-driven price guidance based on recent DHA transactions and comparable listings so you can list with confidence.',
        ],
        [
            'question' => 'Is there a fee for listing with Etihad Marketing?',
            'answer' => 'Our team will explain any applicable marketing or success fees when you submit your details. There is no charge to submit this form.',
        ],
    ],
    'form_submit_label' => 'Continue',
    'status' => 'active',
];
