<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    /**
     * Get all products with categories (for base cache)
     */
    public function getAllWithCategories(): Collection;

    /**
     * Get Query Builder with filters applied (without pagination)
     */
    public function getFilteredQuery(array $filters, string $sort): Builder;

    /**
     * Get products with filters applied
     */
    public function getFiltered(array $filters, string $sort, int $perPage): LengthAwarePaginator;

    /**
     * Apply filters to collection (for working with cached data)
     */
    public function applyFiltersToCollection(Collection $products, array $filters): Collection;

    /**
     * Apply sorting to collection
     */
    public function applySortToCollection(Collection $products, string $sort): Collection;
}
