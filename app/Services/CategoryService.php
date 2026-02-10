<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository
    ) {
    }

    /**
     * Get all categories
     */
    public function getAll(): Collection
    {
        return $this->repository->getAllOrderedByName();
    }
}
