<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->commerce->productName,
            'sku' => $this->faker->unique()->ean8,
            'description' => $this->faker->sentence,
            'stock' => $this->faker->numberBetween(50, 200),
            'is_active' => true,
        ];
    }
}
