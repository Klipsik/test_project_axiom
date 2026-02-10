<?php

namespace Tests\Unit\Repositories;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryRepository(new Category());
    }

    public function test_get_all_ordered_by_name_returns_sorted_categories(): void
    {
        Category::factory()->create(['name' => 'Категория C']);
        Category::factory()->create(['name' => 'Категория A']);
        Category::factory()->create(['name' => 'Категория B']);

        $result = $this->repository->getAllOrderedByName();

        $names = $result->pluck('name')->toArray();
        $this->assertEquals(['Категория A', 'Категория B', 'Категория C'], $names);
    }
}
