<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Asset::class;
    public function definition(): array
    {

        $name = $this->faker->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'image' => $this->faker->imageUrl(),
            'status' => $this->faker->randomElement(['available', 'loaned', 'maintenance']),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'note' => $this->faker->sentence(),
        ];
    }
}
