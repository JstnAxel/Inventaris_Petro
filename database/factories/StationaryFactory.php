<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\User;
use App\Models\Stationary;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stationary>
 */
class StationaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Stationary::class;
     
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'stock' => $this->faker->numberBetween(10, 100),
            'image' => $this->faker->imageUrl(),
            'unit' => $this->faker->randomElement(['pcs', 'box', 'pack']),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'note' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 1000, 100000),
        ];
    }
}
