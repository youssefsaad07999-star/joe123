<?php

namespace Database\Seeders\ProductSeeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()
            ->count(3)
            ->has(ProductVariant::factory()->count(rand(2, 5)), 'variants')
            ->create();

    }
}
