<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private readonly Product $model
    ) {
    }

    /**
     * Get all products with categories (for base cache)
     */
    public function getAllWithCategories(): Collection
    {
        return $this->model->newQuery()
            ->with('category:id,name')
            ->orderBy('id')
            ->get();
    }

    /**
     * Get Query Builder with filters applied (without pagination)
     */
    public function getFilteredQuery(array $filters, string $sort): Builder
    {
        $filterValues = $this->extractFilterValues($filters);

        return $this->model->newQuery()
            ->when($filterValues['q'], fn ($query) => $query->search($filterValues['q']))
            ->when($filterValues['priceFrom'], fn ($query) => $query->priceFrom($filterValues['priceFrom']))
            ->when($filterValues['priceTo'], fn ($query) => $query->priceTo($filterValues['priceTo']))
            ->when($filterValues['categoryId'], fn ($query) => $query->byCategory($filterValues['categoryId']))
            ->when($filterValues['inStock'] !== null, fn ($query) => $query->inStock($filterValues['inStock']))
            ->when($filterValues['ratingFrom'], fn ($query) => $query->ratingFrom($filterValues['ratingFrom']))
            ->sortBy($sort)
            ->with('category:id,name');
    }

    /**
     * Get products with filters applied
     */
    public function getFiltered(array $filters, string $sort, int $perPage): LengthAwarePaginator
    {
        return $this->getFilteredQuery($filters, $sort)->paginate($perPage);
    }

    /**
     * Apply filters to collection (for working with cached data)
     */
    public function applyFiltersToCollection(Collection $products, array $filters): Collection
    {
        $filterValues = $this->extractFilterValues($filters);

        return $products
            ->when($filterValues['q'], fn ($collection) => $collection->filter(
                fn ($product) => mb_strpos(mb_strtolower($product->name), mb_strtolower(trim($filterValues['q']))) !== false
            ))
            ->when($filterValues['priceFrom'], fn ($collection) => $collection->where('price', '>=', $filterValues['priceFrom']))
            ->when($filterValues['priceTo'], fn ($collection) => $collection->where('price', '<=', $filterValues['priceTo']))
            ->when($filterValues['categoryId'], fn ($collection) => $collection->where('category_id', $filterValues['categoryId']))
            ->when($filterValues['inStock'] !== null, fn ($collection) => $collection->where('in_stock', $filterValues['inStock']))
            ->when($filterValues['ratingFrom'], fn ($collection) => $collection->where('rating', '>=', $filterValues['ratingFrom']));
    }

    /**
     * Extract filter values from array (DRY principle)
     */
    private function extractFilterValues(array $filters): array
    {
        return [
            'q' => $filters['q'] ?? null,
            'priceFrom' => $filters['price_from'] ?? null,
            'priceTo' => $filters['price_to'] ?? null,
            'categoryId' => $filters['category_id'] ?? null,
            'inStock' => $filters['in_stock'] ?? null,
            'ratingFrom' => $filters['rating_from'] ?? null,
        ];
    }

    /**
     * Apply sorting to collection
     */
    public function applySortToCollection(Collection $products, string $sort): Collection
    {
        return match ($sort) {
            'price_asc' => $products->sortBy([
                ['price', 'asc'],
                ['id', 'asc'],
            ])->values(),
            'price_desc' => $products->sortBy([
                ['price', 'desc'],
                ['id', 'desc'],
            ])->values(),
            'rating_desc' => $products->sortBy([
                ['rating', 'desc'],
                ['id', 'desc'],
            ])->values(),
            default => $products->sortBy([
                ['created_at', 'desc'],
                ['id', 'desc'],
            ])->values(),
        };
    }
}
