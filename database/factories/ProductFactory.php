<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'name' => $this->faker->sentence(),
            'description' => $this->faker->text(),
            'count' => $this->faker->randomNumber(),
            'price' => $this->faker->randomFloat(),
            'status' => $this->faker->randomElement([ProductStatus::Draft, ProductStatus::Published]),
        ];
    }
}
