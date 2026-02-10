<?php

namespace App\Constants;

class ProductConstants
{
    // Cache keys
    public const CACHE_BASE_KEY = 'products:base';
    public const CACHE_FILTERED_PREFIX = 'products:filtered:';
    public const CACHE_TAG = 'products';

    // Search optimization
    public const SEARCH_SHORT_LENGTH = 3;

    // Filter complexity
    public const SIMPLE_FILTERS_THRESHOLD = 2;

    // Sorting options
    public const SORT_NEWEST = 'newest';
    public const SORT_PRICE_ASC = 'price_asc';
    public const SORT_PRICE_DESC = 'price_desc';
    public const SORT_RATING_DESC = 'rating_desc';

    public const SORT_OPTIONS = [
        self::SORT_NEWEST,
        self::SORT_PRICE_ASC,
        self::SORT_PRICE_DESC,
        self::SORT_RATING_DESC,
    ];
}
