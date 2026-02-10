<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals($category->id, $product->category->id);
    }

    public function test_product_fillable_attributes(): void
    {
        $product = new Product();
        $fillable = ['name', 'price', 'category_id', 'in_stock', 'rating'];

        $this->assertEquals($fillable, $product->getFillable());
    }

    public function test_product_casts(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => '100.50',
            'in_stock' => '1',
            'rating' => '4.5',
        ]);

        $this->assertIsFloat($product->rating);
        $this->assertIsBool($product->in_stock);
        $this->assertEquals('100.50', $product->price);
    }

    public function test_product_can_be_created(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Тестовый товар',
            'price' => 99.99,
            'category_id' => $category->id,
            'in_stock' => true,
            'rating' => 4.5,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Тестовый товар',
            'price' => '99.99',
            'category_id' => $category->id,
            'in_stock' => true,
            'rating' => 4.5,
        ]);
    }

    public function test_product_can_be_updated(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Старое название',
            'category_id' => $category->id,
        ]);

        $product->update(['name' => 'Новое название']);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Новое название',
        ]);
    }

    public function test_product_can_be_deleted(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $product->delete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
