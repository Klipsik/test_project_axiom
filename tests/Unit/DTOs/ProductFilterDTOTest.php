<?php

namespace Tests\Unit\DTOs;

use App\DTOs\ProductFilterDTO;
use Tests\TestCase;

class ProductFilterDTOTest extends TestCase
{
    public function test_from_array_creates_dto_with_all_fields(): void
    {
        $data = [
            'q' => 'test',
            'price_from' => '100.50',
            'price_to' => '200.75',
            'category_id' => '5',
            'in_stock' => 'true',
            'rating_from' => '4.5',
            'sort' => 'price_asc',
            'per_page' => '20',
            'page' => '2',
        ];

        $dto = ProductFilterDTO::fromArray($data);

        $this->assertEquals('test', $dto->q);
        $this->assertEquals(100.50, $dto->priceFrom);
        $this->assertEquals(200.75, $dto->priceTo);
        $this->assertEquals(5, $dto->categoryId);
        $this->assertTrue($dto->inStock);
        $this->assertEquals(4.5, $dto->ratingFrom);
        $this->assertEquals('price_asc', $dto->sort);
        $this->assertEquals(20, $dto->perPage);
        $this->assertEquals(2, $dto->page);
    }

    public function test_from_array_uses_defaults(): void
    {
        $dto = ProductFilterDTO::fromArray([]);

        $this->assertNull($dto->q);
        $this->assertNull($dto->priceFrom);
        $this->assertEquals('newest', $dto->sort);
        $this->assertEquals(15, $dto->perPage);
        $this->assertEquals(1, $dto->page);
    }

    public function test_to_array_filters_null_values(): void
    {
        $dto = new ProductFilterDTO(
            q: 'test',
            priceFrom: 100,
            priceTo: null,
            categoryId: null,
            inStock: null,
            ratingFrom: null,
            sort: 'newest',
            perPage: 15,
            page: 1
        );

        $result = $dto->toArray();

        $this->assertArrayHasKey('q', $result);
        $this->assertArrayHasKey('price_from', $result);
        $this->assertArrayNotHasKey('price_to', $result);
        $this->assertArrayNotHasKey('category_id', $result);
    }
}
