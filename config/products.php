<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Product caching settings
    |
    */

    'cache' => [
        // Base products cache TTL (in minutes)
        'base_ttl' => env('PRODUCT_CACHE_BASE_TTL', 60),

        // Filtered results cache TTL (in minutes)
        'filtered_ttl' => env('PRODUCT_CACHE_FILTERED_TTL', 15),

        // Popular combinations cache TTL (in minutes)
        'popular_ttl' => env('PRODUCT_CACHE_POPULAR_TTL', 30),

        // Cache key prefix
        'prefix' => 'products',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Configuration
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'default_per_page' => 15,
        'max_per_page' => 100,
    ],
];
