<?php

namespace Database\Factories;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'color_id' => Color::inRandomOrder()->first()->id,
            'size_id' => Size::inRandomOrder()->first()->id,
            'sku' => strtoupper($this->faker->bothify('SKU-###??')),
            'stock_quantity' => $this->faker->numberBetween(1, 30),
            'price_override' => $this->faker->randomFloat(2, -50, 100),
            'is_active' => true,
        ];
    }
}
