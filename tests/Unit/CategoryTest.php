<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_products(): void
    {
        $category = Category::factory()->create();
        $products = Product::factory(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->products);
        $this->assertTrue($category->products->contains($products->first()));
    }

    public function test_category_fillable_attributes(): void
    {
        $category = new Category();
        $fillable = ['name'];

        $this->assertEquals($fillable, $category->getFillable());
    }

    public function test_category_can_be_created(): void
    {
        $category = Category::factory()->create(['name' => 'Тестовая категория']);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Тестовая категория',
        ]);
    }

    public function test_category_can_be_updated(): void
    {
        $category = Category::factory()->create(['name' => 'Старое название']);

        $category->update(['name' => 'Новое название']);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Новое название',
        ]);
    }

    public function test_category_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $category->delete();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_deletion_cascades_to_products(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $category->delete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
