<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\Contracts\ProductServiceInterface;

class ProductObserver
{
    public function __construct(
        private readonly ProductServiceInterface $productService
    ) {
    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->productService->invalidateCache();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->productService->invalidateCache($product->id);
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->productService->invalidateCache($product->id);
    }
}
