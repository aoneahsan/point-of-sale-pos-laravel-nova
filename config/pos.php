<?php

return [
    /*
    |--------------------------------------------------------------------------
    | POS Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the Point of Sale system
    |
    */

    // Sale reference prefix
    'sale_reference_prefix' => env('POS_SALE_PREFIX', 'SALE'),

    // Default tax rate (percentage)
    'default_tax_rate' => env('POS_DEFAULT_TAX_RATE', 10.00),

    // Currency
    'currency' => env('POS_CURRENCY', 'USD'),
    'currency_symbol' => env('POS_CURRENCY_SYMBOL', '$'),

    // Loyalty points
    'enable_loyalty_points' => env('POS_ENABLE_LOYALTY_POINTS', true),
    'loyalty_points_rate' => env('POS_LOYALTY_POINTS_RATE', 0.1), // 10% of sale total

    // Receipt settings
    'receipt_header' => env('POS_RECEIPT_HEADER', 'POS System'),
    'receipt_footer' => env('POS_RECEIPT_FOOTER', 'Thank you for your business!'),
    'print_receipt_auto' => env('POS_PRINT_RECEIPT_AUTO', true),

    // Stock management
    'allow_negative_stock' => env('POS_ALLOW_NEGATIVE_STOCK', false),
    'low_stock_threshold' => env('POS_LOW_STOCK_THRESHOLD', 10),
    'track_inventory_default' => env('POS_TRACK_INVENTORY', true),

    // Cash drawer
    'require_cash_drawer' => env('POS_REQUIRE_CASH_DRAWER', true),

    // Barcode
    'barcode_symbology' => env('POS_BARCODE_SYMBOLOGY', 'CODE128'),
];
