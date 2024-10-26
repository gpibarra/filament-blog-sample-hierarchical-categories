<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'slug' => fn (array $attributes) => \Str::slug($attributes['name']),
            'parent_id' => (rand(0, 1) > 0.75) ? null : \App\Models\Category::query()->inRandomOrder()->first()?->id,
        ];
    }
}
