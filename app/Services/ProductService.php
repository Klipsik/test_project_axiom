<?php

namespace App\Services;

use App\DTOs\ProductFilterDTO;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository,
        private readonly ProductCacheService $cacheService,
        private readonly ConfigRepository $config
    ) {
    }

    /**
     * Get products list with filters and pagination
     */
    public function getProducts(array $filters): LengthAwarePaginator
    {
        $dto = ProductFilterDTO::fromArray($filters);

        $perPage = min(
            $dto->perPage,
            $this->config->get('products.pagination.max_per_page', 100)
        );

        return $this->cacheService->getFilteredProducts(
            $dto->toArray(),
            $dto->sort,
            $perPage,
            $dto->page
        );
    }

    /**
     * Invalidate product cache (on create/update/delete)
     */
    public function invalidateCache(?int $productId = null): void
    {
        if ($productId) {
            $this->cacheService->invalidateProductCache($productId);
        } else {
            $this->cacheService->invalidateCache();
        }
    }
}
