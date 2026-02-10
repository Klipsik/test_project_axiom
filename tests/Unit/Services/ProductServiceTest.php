<?php

namespace Tests\Unit\Services;

use App\DTOs\ProductFilterDTO;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductCacheService;
use App\Services\ProductService;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_products_calls_cache_service_with_correct_parameters(): void
    {
        $repository = $this->createMock(ProductRepositoryInterface::class);
        $cacheService = $this->createMock(ProductCacheService::class);
        $config = $this->createMock(ConfigRepository::class);

        $config->expects($this->once())
            ->method('get')
            ->with('products.pagination.max_per_page', 100)
            ->willReturn(100);

        $filters = ['per_page' => 15, 'page' => 1];
        $expectedPaginator = $this->createMock(LengthAwarePaginator::class);

        $cacheService->expects($this->once())
            ->method('getFilteredProducts')
            ->with(
                $this->isType('array'),
                $this->isType('string'),
                $this->equalTo(15),
                $this->equalTo(1)
            )
            ->willReturn($expectedPaginator);

        $service = new ProductService($repository, $cacheService, $config);
        $result = $service->getProducts($filters);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_get_products_respects_max_per_page_limit(): void
    {
        $repository = $this->createMock(ProductRepositoryInterface::class);
        $cacheService = $this->createMock(ProductCacheService::class);
        $config = $this->createMock(ConfigRepository::class);

        $config->expects($this->once())
            ->method('get')
            ->with('products.pagination.max_per_page', 100)
            ->willReturn(100);

        $filters = ['per_page' => 200, 'page' => 1];
        $expectedPaginator = $this->createMock(LengthAwarePaginator::class);

        $cacheService->expects($this->once())
            ->method('getFilteredProducts')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->equalTo(100), // Should be limited to 100
                $this->anything()
            )
            ->willReturn($expectedPaginator);

        $service = new ProductService($repository, $cacheService, $config);
        $service->getProducts($filters);
    }

    public function test_invalidate_cache_without_product_id(): void
    {
        $repository = $this->createMock(ProductRepositoryInterface::class);
        $cacheService = $this->createMock(ProductCacheService::class);
        $config = $this->createMock(ConfigRepository::class);

        $cacheService->expects($this->once())
            ->method('invalidateCache');

        $cacheService->expects($this->never())
            ->method('invalidateProductCache');

        $service = new ProductService($repository, $cacheService, $config);
        $service->invalidateCache();
    }

    public function test_invalidate_cache_with_product_id(): void
    {
        $repository = $this->createMock(ProductRepositoryInterface::class);
        $cacheService = $this->createMock(ProductCacheService::class);
        $config = $this->createMock(ConfigRepository::class);

        $cacheService->expects($this->never())
            ->method('invalidateCache');

        $cacheService->expects($this->once())
            ->method('invalidateProductCache')
            ->with(123);

        $service = new ProductService($repository, $cacheService, $config);
        $service->invalidateCache(123);
    }
}
