<?php

namespace Tests\Unit\Repositories;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductRepository(new Product());
    }

    public function test_get_all_with_categories_returns_collection_with_relations(): void
    {
        $category = Category::factory()->create();
        Product::factory(3)->create(['category_id' => $category->id]);

        $result = $this->repository->getAllWithCategories();

        $this->assertCount(3, $result);
        $this->assertTrue($result->first()->relationLoaded('category'));
    }

    public function test_apply_filters_to_collection_filters_by_price_from(): void
    {
        $category = Category::factory()->create();
        $products = Product::factory()->createMany([
            ['price' => 100, 'category_id' => $category->id],
            ['price' => 200, 'category_id' => $category->id],
            ['price' => 50, 'category_id' => $category->id],
        ]);

        $filtered = $this->repository->applyFiltersToCollection(
            $products,
            ['price_from' => 150]
        );

        $this->assertCount(1, $filtered);
        $this->assertEquals(200, $filtered->first()->price);
    }

    public function test_apply_filters_to_collection_filters_by_category(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $products = Product::factory()->createMany([
            ['category_id' => $category1->id],
            ['category_id' => $category2->id],
            ['category_id' => $category1->id],
        ]);

        $filtered = $this->repository->applyFiltersToCollection(
            $products,
            ['category_id' => $category1->id]
        );

        $this->assertCount(2, $filtered);
        $this->assertTrue($filtered->every(fn ($p) => $p->category_id === $category1->id));
    }

    public function test_apply_sort_to_collection_sorts_by_price_asc(): void
    {
        $category = Category::factory()->create();
        $products = Product::factory()->createMany([
            ['price' => 300, 'category_id' => $category->id],
            ['price' => 100, 'category_id' => $category->id],
            ['price' => 200, 'category_id' => $category->id],
        ]);

        $sorted = $this->repository->applySortToCollection($products, 'price_asc');

        $prices = $sorted->pluck('price')->toArray();
        $this->assertEquals([100, 200, 300], $prices);
    }

    public function test_extract_filter_values_returns_correct_structure(): void
    {
        $reflection = new \ReflectionClass($this->repository);
        $method = $reflection->getMethod('extractFilterValues');
        $method->setAccessible(true);

        $filters = [
            'q' => 'test',
            'price_from' => 100,
            'price_to' => 200,
            'category_id' => 1,
            'in_stock' => true,
            'rating_from' => 4.0,
        ];

        $result = $method->invoke($this->repository, $filters);

        $this->assertEquals('test', $result['q']);
        $this->assertEquals(100, $result['priceFrom']);
        $this->assertEquals(200, $result['priceTo']);
        $this->assertEquals(1, $result['categoryId']);
        $this->assertTrue($result['inStock']);
        $this->assertEquals(4.0, $result['ratingFrom']);
    }
}
