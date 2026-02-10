<?php

namespace Tests\Unit\Observers;

use App\Models\Category;
use App\Models\Product;
use App\Observers\ProductObserver;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_created_event_invalidates_cache(): void
    {
        $service = $this->createMock(ProductServiceInterface::class);
        $service->expects($this->once())
            ->method('invalidateCache')
            ->with(null);

        $observer = new ProductObserver($service);
        $category = Category::factory()->create();
        $product = Product::factory()->make(['category_id' => $category->id]);

        $observer->created($product);
    }

    public function test_updated_event_invalidates_product_cache(): void
    {
        $service = $this->createMock(ProductServiceInterface::class);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $service->expects($this->once())
            ->method('invalidateCache')
            ->with($product->id);

        $observer = new ProductObserver($service);
        $observer->updated($product);
    }

    public function test_deleted_event_invalidates_product_cache(): void
    {
        $service = $this->createMock(ProductServiceInterface::class);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $service->expects($this->once())
            ->method('invalidateCache')
            ->with($product->id);

        $observer = new ProductObserver($service);
        $observer->deleted($product);
    }
}
