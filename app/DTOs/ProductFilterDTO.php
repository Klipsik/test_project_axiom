<?php

namespace App\DTOs;

readonly class ProductFilterDTO
{
    public function __construct(
        public ?string $q = null,
        public ?float $priceFrom = null,
        public ?float $priceTo = null,
        public ?int $categoryId = null,
        public ?bool $inStock = null,
        public ?float $ratingFrom = null,
        public string $sort = 'newest',
        public int $perPage = 15,
        public int $page = 1,
    ) {
    }

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            q: $data['q'] ?? null,
            priceFrom: isset($data['price_from']) ? (float) $data['price_from'] : null,
            priceTo: isset($data['price_to']) ? (float) $data['price_to'] : null,
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            inStock: isset($data['in_stock']) ? filter_var($data['in_stock'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null,
            ratingFrom: isset($data['rating_from']) ? (float) $data['rating_from'] : null,
            sort: $data['sort'] ?? 'newest',
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 15,
            page: isset($data['page']) ? (int) $data['page'] : 1,
        );
    }

    /**
     * Convert DTO to array for repository
     */
    public function toArray(): array
    {
        return array_filter([
            'q' => $this->q,
            'price_from' => $this->priceFrom,
            'price_to' => $this->priceTo,
            'category_id' => $this->categoryId,
            'in_stock' => $this->inStock,
            'rating_from' => $this->ratingFrom,
        ], fn ($value) => $value !== null);
    }
}
