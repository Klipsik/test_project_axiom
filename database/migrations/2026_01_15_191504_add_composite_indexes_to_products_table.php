<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Составные индексы для оптимизации частых комбинаций фильтров
            $table->index(['category_id', 'in_stock'], 'products_category_stock_idx');
            $table->index(['price', 'in_stock'], 'products_price_stock_idx');
            $table->index(['rating', 'in_stock'], 'products_rating_stock_idx');
            $table->index(['created_at', 'id'], 'products_created_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_category_stock_idx');
            $table->dropIndex('products_price_stock_idx');
            $table->dropIndex('products_rating_stock_idx');
            $table->dropIndex('products_created_id_idx');
        });
    }
};
