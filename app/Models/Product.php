<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'category_id',
        'in_stock',
        'rating',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'in_stock' => 'boolean',
        'rating' => 'float',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope a query to search by name (optimized search from string start)
     */
    #[Scope]
    protected function search(Builder $query, ?string $search): void
    {
        $query->when($search, function ($q) use ($search) {
            $search = trim($search);

            // If search is short, use LIKE from string start (faster)
            $q->when(
                strlen($search) <= \App\Constants\ProductConstants::SEARCH_SHORT_LENGTH,
                fn ($query) => $query->where('name', 'LIKE', $search.'%'),
                fn ($query) => $query->where('name', 'LIKE', '%'.$search.'%')
            );
        });
    }

    /**
     * Scope a query to filter by price from
     */
    #[Scope]
    protected function priceFrom(Builder $query, ?float $priceFrom): void
    {
        $query->when($priceFrom, fn ($q) => $q->where('price', '>=', $priceFrom));
    }

    /**
     * Scope a query to filter by price to
     */
    #[Scope]
    protected function priceTo(Builder $query, ?float $priceTo): void
    {
        $query->when($priceTo, fn ($q) => $q->where('price', '<=', $priceTo));
    }

    /**
     * Scope a query to filter by category
     */
    #[Scope]
    protected function byCategory(Builder $query, ?int $categoryId): void
    {
        $query->when($categoryId, fn ($q) => $q->where('category_id', $categoryId));
    }

    /**
     * Scope a query to filter by stock status
     */
    #[Scope]
    protected function inStock(Builder $query, ?bool $inStock): void
    {
        $query->when($inStock !== null, fn ($q) => $q->where('in_stock', $inStock));
    }

    /**
     * Scope a query to filter by rating from
     */
    #[Scope]
    protected function ratingFrom(Builder $query, ?float $ratingFrom): void
    {
        $query->when($ratingFrom, fn ($q) => $q->where('rating', '>=', $ratingFrom));
    }

    /**
     * Scope a query to apply sorting
     */
    #[Scope]
    protected function sortBy(Builder $query, string $sort = 'newest'): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc')->orderBy('id', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc')->orderBy('id', 'desc'),
            'rating_desc' => $query->orderBy('rating', 'desc')->orderBy('id', 'desc'),
            default => $query->orderBy('created_at', 'desc')->orderBy('id', 'desc'),
        };
    }
}
