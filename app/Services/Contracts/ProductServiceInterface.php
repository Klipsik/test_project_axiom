<?php

namespace App\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    /**
     * Get products list with filters and pagination
     */
    public function getProducts(array $filters): LengthAwarePaginator;

    /**
     * Invalidate product cache (on create/update/delete)
     */
    public function invalidateCache(?int $productId = null): void;
}
