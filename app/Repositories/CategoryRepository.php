<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        private readonly Category $model
    ) {
    }

    /**
     * Get all categories ordered by name
     */
    public function getAllOrderedByName(): Collection
    {
        return $this->model->newQuery()
            ->orderBy('name')
            ->get();
    }
}
