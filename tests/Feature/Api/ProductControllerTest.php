<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Создаем тестовые данные
        $this->categories = Category::factory(5)->create();
        $this->products = Product::factory(20)->create([
            'category_id' => fn () => $this->categories->random()->id,
        ]);
    }

    public function test_can_get_products_list(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'price',
                        'category_id',
                        'in_stock',
                        'rating',
                        'created_at',
                        'updated_at',
                        'category',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    public function test_can_filter_products_by_search_query(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Уникальный товар для поиска',
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/products?q=Уникальный');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $product->id);
    }

    public function test_can_filter_products_by_price_from(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['price' => 100, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 200, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 50, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?price_from=150');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertTrue(collect($data)->every(fn ($item) => $item['price'] >= 150));
    }

    public function test_can_filter_products_by_price_to(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['price' => 100, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 200, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 300, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?price_to=150');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertTrue(collect($data)->every(fn ($item) => $item['price'] <= 150));
    }

    public function test_can_filter_products_by_price_range(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['price' => 100, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 200, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 300, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 400, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?price_from=150&price_to=250');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertTrue(collect($data)->every(fn ($item) => 
            $item['price'] >= 150 && $item['price'] <= 250
        ));
    }

    public function test_can_filter_products_by_category(): void
    {
        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();
        $productsInCategory = Product::factory(3)->create(['category_id' => $category->id]);
        Product::factory(5)->create(['category_id' => $otherCategory->id]); // Другие товары

        $response = $this->getJson("/api/products?category_id={$category->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $data = $response->json('data');
        $this->assertTrue(collect($data)->every(fn ($item) => $item['category_id'] === $category->id));
    }

    public function test_can_filter_products_by_in_stock(): void
    {
        $category = Category::factory()->create();
        Product::factory(3)->create(['in_stock' => true, 'category_id' => $category->id]);
        Product::factory(2)->create(['in_stock' => false, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?in_stock=true');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertTrue(collect($data)->every(fn ($item) => $item['in_stock'] === true));
    }

    public function test_can_filter_products_by_rating_from(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['rating' => 2.5, 'category_id' => $category->id]);
        Product::factory()->create(['rating' => 4.0, 'category_id' => $category->id]);
        Product::factory()->create(['rating' => 4.5, 'category_id' => $category->id]);
        Product::factory()->create(['rating' => 1.0, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?rating_from=4.0');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertTrue(collect($data)->every(fn ($item) => $item['rating'] >= 4.0));
    }

    public function test_can_sort_products_by_price_asc(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['price' => 300, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 100, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 200, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?sort=price_asc');

        $response->assertStatus(200);
        $prices = collect($response->json('data'))->pluck('price')->toArray();
        $sortedPrices = $prices;
        sort($sortedPrices);
        $this->assertEquals($sortedPrices, $prices);
    }

    public function test_can_sort_products_by_price_desc(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['price' => 100, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 300, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 200, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?sort=price_desc');

        $response->assertStatus(200);
        $prices = collect($response->json('data'))->pluck('price')->toArray();
        $sortedPrices = $prices;
        rsort($sortedPrices);
        $this->assertEquals($sortedPrices, $prices);
    }

    public function test_can_sort_products_by_rating_desc(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['rating' => 3.0, 'category_id' => $category->id]);
        Product::factory()->create(['rating' => 5.0, 'category_id' => $category->id]);
        Product::factory()->create(['rating' => 4.0, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?sort=rating_desc');

        $response->assertStatus(200);
        $ratings = collect($response->json('data'))->pluck('rating')->toArray();
        $sortedRatings = $ratings;
        rsort($sortedRatings);
        $this->assertEquals($sortedRatings, $ratings);
    }

    public function test_can_sort_products_by_newest(): void
    {
        $category = Category::factory()->create();
        $oldProduct = Product::factory()->create([
            'created_at' => now()->subDays(5),
            'category_id' => $category->id,
        ]);
        $newProduct = Product::factory()->create([
            'created_at' => now()->addSecond(),
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/products?sort=newest');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        // Проверяем что товары отсортированы по убыванию даты создания
        $createdAts = collect($data)->map(fn ($item) => strtotime($item['created_at']))->toArray();
        $sortedCreatedAts = $createdAts;
        rsort($sortedCreatedAts);
        $this->assertEquals($sortedCreatedAts, $createdAts, 'Товары должны быть отсортированы по убыванию даты создания');
    }

    public function test_pagination_works(): void
    {
        $category = Category::factory()->create();
        Product::factory(25)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products?per_page=10');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.per_page', 10);
        $response->assertJsonPath('meta.current_page', 1);
    }

    public function test_can_change_page(): void
    {
        $category = Category::factory()->create();
        Product::factory(25)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products?per_page=10&page=2');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.current_page', 2);
    }

    public function test_per_page_limit_is_100(): void
    {
        $category = Category::factory()->create();
        Product::factory(150)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products?per_page=200');

        $response->assertStatus(200);
        $response->assertJsonPath('meta.per_page', 100);
        $this->assertLessThanOrEqual(100, count($response->json('data')));
    }

    public function test_products_include_category_relation(): void
    {
        $category = Category::factory()->create(['name' => 'Тестовая категория']);
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->getJson("/api/products?category_id={$category->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.category.id', $category->id);
        $response->assertJsonPath('data.0.category.name', 'Тестовая категория');
    }

    public function test_combined_filters_work(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create([
            'category_id' => $category->id,
            'price' => 150,
            'in_stock' => true,
            'rating' => 4.5,
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'price' => 200,
            'in_stock' => false,
            'rating' => 3.0,
        ]);

        $response = $this->getJson("/api/products?category_id={$category->id}&price_from=100&price_to=200&in_stock=true&rating_from=4.0");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
}
