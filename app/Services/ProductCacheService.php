<?php

namespace App\Services;

use App\Constants\ProductConstants;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductCacheService
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository,
        private readonly CacheRepository $cache,
        private readonly ConfigRepository $config
    ) {
    }

    /**
     * Get base products collection (cached for a long time)
     */
    public function getBaseProducts(): Collection
    {
        $ttl = $this->config->get('products.cache.base_ttl', 60);

        $cache = $this->getCacheStore();

        return $cache->remember(
            ProductConstants::CACHE_BASE_KEY,
            now()->addMinutes($ttl),
            fn () => $this->repository->getAllWithCategories()
        );
    }

    /**
     * Get filtered products with smart caching strategy
     * 
     * Strategy:
     * 1. If filters are few or none - use base cache + in-memory filtering
     * 2. If filters are many - use cache for specific combination
     * 3. For frequent combinations - longer cache TTL
     */
    public function getFilteredProducts(array $filters, string $sort, int $perPage, int $page = 1): LengthAwarePaginator
    {
        // Determine filter complexity
        $filtersCount = $this->countActiveFilters($filters);
        $hasSearch = !empty($filters['q']);

        // If filters are few or simple - use base cache + filtering
        if ($filtersCount <= ProductConstants::SIMPLE_FILTERS_THRESHOLD && !$hasSearch) {
            return $this->getFromBaseCache($filters, $sort, $perPage, $page);
        }

        // For complex queries - cache the query result
        return $this->getFromFilteredCache($filters, $sort, $perPage, $page);
    }

    /**
     * Get data from base cache with filters applied
     */
    private function getFromBaseCache(array $filters, string $sort, int $perPage, int $page): LengthAwarePaginator
    {
        // Build cache key for sorted and filtered result (without pagination)
        $cacheKey = $this->buildBaseCacheKey($filters, $sort);
        $ttl = $this->config->get('products.cache.base_ttl', 60);
        
        $cache = $this->getCacheStore();
        
        // Cache the sorted and filtered collection (without pagination)
        $sortedProducts = $cache->remember(
            $cacheKey,
            now()->addMinutes($ttl),
            function () use ($filters, $sort) {
                $baseProducts = $this->getBaseProducts();
                
                // Apply filters to collection
                $filtered = $this->repository->applyFiltersToCollection($baseProducts, $filters);
                
                // Apply sorting
                return $this->repository->applySortToCollection($filtered, $sort);
            }
        );

        // Apply pagination to cached data
        return $this->paginateCollection($sortedProducts, $perPage, $page);
    }
    
    /**
     * Build cache key for base cache with filters and sort
     */
    private function buildBaseCacheKey(array $filters, string $sort): string
    {
        // Exclude pagination parameters
        $cacheFilters = array_diff_key($filters, array_flip(['page', 'per_page']));
        
        // Sort filters for key consistency
        ksort($cacheFilters);
        
        $hash = md5(json_encode($cacheFilters) . $sort);
        
        return ProductConstants::CACHE_BASE_KEY . ':filtered:' . $hash;
    }

    /**
     * Get data from filtered results cache
     */
    private function getFromFilteredCache(array $filters, string $sort, int $perPage, int $page): LengthAwarePaginator
    {
        $cacheKey = $this->buildFilteredCacheKey($filters, $sort, $perPage);
        $ttl = $this->getFilteredCacheTtl($filters);

        $cache = $this->getCacheStore();

        // Cache entire result without pagination, then apply pagination
        $allProducts = $cache->remember(
            $cacheKey,
            now()->addMinutes($ttl),
            function () use ($filters, $sort) {
                // Get all products without pagination
                $query = $this->repository->getFilteredQuery($filters, $sort);
                return $query->get();
            }
        );

        // Apply pagination to cached data
        return $this->paginateCollection($allProducts, $perPage, $page);
    }

    /**
     * Invalidate all product cache
     */
    public function invalidateCache(): void
    {
        $driver = $this->config->get('cache.default');
        $tagSupportedDrivers = ['redis', 'memcached'];

        if (in_array($driver, $tagSupportedDrivers)) {
            try {
                $this->cache->tags([ProductConstants::CACHE_TAG])->flush();
                return;
            } catch (\BadMethodCallException $e) {
                // Fallback if tags are not actually supported
            }
        }

        // Otherwise remove keys manually
        $this->flushCacheKeys();
    }

    /**
     * Get cache store instance with tag support (if available)
     */
    private function getCacheStore()
    {
        $driver = $this->config->get('cache.default');

        // Check if driver supports tags (Redis, Memcached)
        $tagSupportedDrivers = ['redis', 'memcached'];

        if (in_array($driver, $tagSupportedDrivers)) {
            try {
                return $this->cache->tags([ProductConstants::CACHE_TAG]);
            } catch (\BadMethodCallException $e) {
                // Fallback if tags are not actually supported
                return $this->cache;
            }
        }

        return $this->cache;
    }

    /**
     * Remove all product cache keys (fallback for drivers without tags)
     */
    private function flushCacheKeys(): void
    {
        // Remove base cache
        $this->cache->forget(ProductConstants::CACHE_BASE_KEY);

        // Note: For filtered cache keys (both base filtered and complex filtered),
        // we would need to track them or use a pattern-based deletion.
        // For drivers without tags, it's recommended to use Redis/Memcached in production.
        // Here we clear the base cache which will be regenerated on next request.
        // Filtered caches will expire naturally based on their TTL.
    }

    /**
     * Invalidate cache for specific product (on update)
     */
    public function invalidateProductCache(int $productId): void
    {
        // Invalidate entire cache, as product could be in any set
        $this->invalidateCache();
    }

    /**
     * Count active filters
     */
    private function countActiveFilters(array $filters): int
    {
        $count = 0;
        $ignored = ['sort', 'per_page', 'page'];

        foreach ($filters as $key => $value) {
            if (!in_array($key, $ignored) && $value !== null && $value !== '') {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Build cache key for filtered results
     */
    private function buildFilteredCacheKey(array $filters, string $sort, int $perPage): string
    {
        // Sort filters for key consistency
        ksort($filters);
        
        // Exclude pagination parameters from key (cache first page)
        $cacheFilters = array_diff_key($filters, array_flip(['page', 'per_page']));
        
        $hash = md5(json_encode($cacheFilters) . $sort . $perPage);
        
        return ProductConstants::CACHE_FILTERED_PREFIX . $hash;
    }

    /**
     * Get TTL for filtered cache
     */
    private function getFilteredCacheTtl(array $filters): int
    {
        // If there's text search - cache for shorter time
        if (!empty($filters['q'])) {
            return $this->config->get('products.cache.filtered_ttl', 15);
        }

        // For others - use popular TTL
        return $this->config->get('products.cache.popular_ttl', 30);
    }

    /**
     * Create pagination from collection
     */
    private function paginateCollection(Collection $items, int $perPage, int $page): LengthAwarePaginator
    {
        $total = $items->count();
        $offset = ($page - 1) * $perPage;
        $itemsForPage = $items->slice($offset, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForPage,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }
}
