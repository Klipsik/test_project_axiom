<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Resources\ProductResource;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductServiceInterface $productService
    ) {
    }

    public function index(ProductIndexRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $products = $this->productService->getProducts($validated);

        return ProductResource::collection($products);
    }
}
