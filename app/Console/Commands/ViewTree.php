<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;

class ViewTree extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:view-tree';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roots = Category::getAllHierarchy();

        $countRecursive = function (Category $c) use (&$countRecursive): int {
            return 1 + ($c->relationLoaded('children') ? $c->children->sum($countRecursive) : 0);
        };
        $printRecursive = function (Category $c, int $depth = 0) use (&$printRecursive): void {
            $current = "#{$c->id} {$c->name}";
            $parent = $c->relationLoaded('parent') && $c->parent ? " (parent: #{$c->parent->id})" : '';
            $countChildren = $c->relationLoaded('parent') ? " ({$c->children->count()} children)" : '';
            $print = "{$current}{$parent}{$countChildren}";
            $this->info(str_repeat('  ', $depth).$print);
            if ($c->relationLoaded('children')) {
                $c->children->each(fn (Category $cc) => $printRecursive($cc, $depth + 1));
            }
        };

        $this->info('Total categories: '.Category::count());
        $this->info('Total categories in tree: '.$roots->sum($countRecursive));

        $roots->each(fn (Category $c) => $printRecursive($c));

    }
}
