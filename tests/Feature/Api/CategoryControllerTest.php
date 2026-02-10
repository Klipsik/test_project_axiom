<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_categories_list(): void
    {
        Category::factory(5)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ]);
    }

    public function test_categories_are_sorted_by_name(): void
    {
        Category::factory()->create(['name' => 'Категория C']);
        Category::factory()->create(['name' => 'Категория A']);
        Category::factory()->create(['name' => 'Категория B']);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);
        $names = collect($response->json())->pluck('name')->toArray();
        $sortedNames = $names;
        sort($sortedNames);
        $this->assertEquals($sortedNames, $names);
    }

    public function test_returns_empty_array_when_no_categories(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJson([]);
    }
}
