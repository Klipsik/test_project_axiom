<?php

namespace Tests\Unit\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\CategoryService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_calls_repository(): void
    {
        $repository = $this->createMock(CategoryRepositoryInterface::class);
        $expectedCollection = $this->createMock(Collection::class);

        $repository->expects($this->once())
            ->method('getAllOrderedByName')
            ->willReturn($expectedCollection);

        $service = new CategoryService($repository);
        $result = $service->getAll();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame($expectedCollection, $result);
    }
}
