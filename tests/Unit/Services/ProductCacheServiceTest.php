<?php

namespace Tests\Unit\Services;

use App\Constants\ProductConstants;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Services\ProductCacheService;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ProductCacheServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductCacheService $service;
    private ProductRepositoryInterface $repository;
    private CacheRepository $cache;
    private ConfigRepository $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductRepository(new Product());
        $this->cache = Cache::store();
        $this->config = $this->app->make(ConfigRepository::class);
        $this->service = new ProductCacheService($this->repository, $this->cache, $this->config);
    }

    public function test_get_base_products_returns_collection(): void
    {
        $category = Category::factory()->create();
        Product::factory(3)->create(['category_id' => $category->id]);

        $result = $this->service->getBaseProducts();

        $this->assertCount(3, $result);
        $this->assertTrue($result->first()->relationLoaded('category'));
    }

    public function test_invalidate_cache_clears_base_cache(): void
    {
        $category = Category::factory()->create();
        Product::factory(2)->create(['category_id' => $category->id]);

        // Populate cache
        $this->service->getBaseProducts();
        $this->assertTrue($this->cache->has(ProductConstants::CACHE_BASE_KEY));

        // Invalidate cache
        $this->service->invalidateCache();
        $this->assertFalse($this->cache->has(ProductConstants::CACHE_BASE_KEY));
    }

    public function test_get_filtered_products_uses_base_cache_for_simple_filters(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['price' => 100, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 200, 'category_id' => $category->id]);

        $filters = ['price_from' => 150];
        $result = $this->service->getFilteredProducts($filters, 'newest', 15, 1);

        $this->assertCount(1, $result->items());
        $this->assertEquals(200, $result->items()[0]->price);
    }

    public function test_get_filtered_products_uses_filtered_cache_for_complex_filters(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100,
            'category_id' => $category->id,
        ]);

        $filters = ['q' => 'Test', 'price_from' => 50, 'price_to' => 200, 'category_id' => $category->id];
        $result = $this->service->getFilteredProducts($filters, 'newest', 15, 1);

        $this->assertCount(1, $result->items());
    }
}
