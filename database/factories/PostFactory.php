<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->sentence,
            'content' => $this->faker->paragraphs(3, true),
            'user_id' => \App\Models\User::query()->inRandomOrder()->first()?->id,
            'published_at' => (rand(0, 1) > 0.75) ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'main_category_id' => \App\Models\Category::query()->inRandomOrder()->first()?->id,
        ];
    }
}
