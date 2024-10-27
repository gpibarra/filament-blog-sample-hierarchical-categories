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

        $renameRecursive = function (\App\Models\Category $c, $deph = 0, $previuos = []) use (&$renameRecursive): void {
            if ($c->relationLoaded('children')) {
                $c->children->each(fn (\App\Models\Category $cc) => $renameRecursive($cc, $deph + 1, [...$previuos, $c->id]));
            }
            $c->name = 'Category '.($deph ? implode(' > ', $previuos).' > ' : '').($c->id);
            $c->slug = \Illuminate\Support\Str::slug($c->name);
            $c->save();
        };
        $roots = \App\Models\Category::getAllHierarchy();
        $roots->each(fn (\App\Models\Category $c) => $renameRecursive($c));
    }
}
