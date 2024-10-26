<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Artisan::call('make:filament-user', [
            '--name' => 'admin',
            '--email' => 'admin@mail.com',
            '--password' => 'pass123',
        ]);

        $users = User::factory(10)->create();
        // $categories = \App\Models\Category::factory(10)->create();
        $categories = \Illuminate\Support\Collection::times(
            10,
            fn () => \App\Models\Category::factory()->create()
        );
        $post = \App\Models\Post::factory(30)->create();
        $post->each(
            fn (\App\Models\Post $post) => $post->categories()->attach(
                $categories->filter(
                    fn (\App\Models\Category $category) => $category->main_parent_id === null
                )->random()->take(rand(0, 3))->pluck('id')->toArray()
            )
        );
    }
}
