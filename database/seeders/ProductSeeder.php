<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем категории
        $categories = Category::factory(10)->create();

        // Создаем 1000 товаров
        Product::factory(1000)->create([
            'category_id' => fn () => $categories->random()->id,
        ]);
    }
}
