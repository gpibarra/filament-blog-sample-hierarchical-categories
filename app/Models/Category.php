<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];

    //region relations
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function siblings(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'parent_id');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'category_post');
    }

    public function mainPosts(): HasMany
    {
        return $this->hasMany(Post::class, 'main_category_id');
    }
    //endregion

    //region helpers
    public function loadHierarchy(): self
    {
        $allHierarchy = Category::getAllHierarchy();
        $fnFind = function (EloquentCollection $c, Category $find) use (&$fnFind): ?Category {
            return $c->map(function (Category $c) use ($find, &$fnFind) {
                if ($c->id === $find->id) {
                    return $c;
                }
                if ($c->relationLoaded('children') && $c->children) {
                    return $fnFind($c->children, $find);
                }
            })->filter()->first();
        };
        // ensure path children of parent in item 0
        $fnSortChildrenInParent = function (Category $c) use (&$fnSortChildrenInParent): void {
            if ($c->relationLoaded('parent') && $c->parent) {
                $fnSortChildrenInParent($c->parent);
                $collectionChildrenOriginal = $c->parent->children;
                $collectionChildrenNew = EloquentCollection::make([$c])->concat($collectionChildrenOriginal->filter(fn (Category $ch) => $ch->id !== $c->id));
                $c->parent->setRelation('children', $collectionChildrenNew);
            }
        };

        $found = $fnFind($allHierarchy, $this);
        if ($found) {
            $this->setRelation('parent', $found->parent);
            if ($found->relationLoaded('children')) {
                $this->setRelation('children', $found->children);
                // fix references in children
                $this->children->each(fn (Category $ch) => $ch->setRelation('parent', $this));
            }
            // fix references in parent
            if ($this->parent) {
                $collectionChildrenOriginal = $this->parent->children;
                $this->parent->setRelation('children', $collectionChildrenOriginal->map(fn (Category $ch) => $ch->id === $this->id ? $this : $ch));
            }
            // ensure path children of parent in item 0
            $fnSortChildrenInParent($this);
            // load siblings
            $grandParent = $this->grand_parent;
            $siblings = $allHierarchy->filter(fn (Category $c) => $c->id === $grandParent->id);
            $this->setRelation('siblings', $siblings);
        }

        return $this;
    }

    public static function getAllHierarchy(): EloquentCollection
    {
        $roots = Category::query()
            ->whereNull('parent_id')
            ->get();
        $leaves = Category::query()
            ->whereNotNull('parent_id')
            ->get();
        $all = $roots->concat($leaves);
        $allById = $all->keyBy('id');
        $roots->each(fn (Category $c) => $c->setRelation('parent', null));
        $all->each(fn (Category $c) => $c->setRelation('children', EloquentCollection::make([])));
        foreach ($leaves as &$leaf) {
            if (! isset($allById[$leaf->parent_id])) {
                continue;
            }
            $parent = $allById[$leaf->parent_id];
            $parent->children->push($leaf);
            $leaf->setRelation('parent', $parent);
        }

        return $roots;
    }

    public function getAllParentsAttribute()
    {
        $parents = EloquentCollection::make([]);
        $fnPushParents = function (Category $c) use (&$fnPushParents, &$parents): void {
            if ($c->relationLoaded('parent') && $c->parent) {
                $parents->push($c->parent);
                $fnPushParents($c->parent);
            }
        };

        $fnPushParents($this);

        return $parents;
    }

    public function getAllChildrenAttribute()
    {
        $children = EloquentCollection::make([]);
        $fnPushChildren = function (Category $c) use (&$fnPushChildren, &$children): void {
            if ($c->relationLoaded('children') && $c->children) {
                $c->children->each(fn (Category $child) => $children->push($child));
                $c->children->each(fn (Category $child) => $fnPushChildren($child));
            }
        };

        $fnPushChildren($this);

        return $children;
    }

    public function getGrandParentAttribute()
    {
        $fnFindParent = function (Category $c) use (&$fnFindParent): Category {
            return $c->relationLoaded('parent') && $c->parent ?
                $fnFindParent($c->parent) :
                $c;
        };

        return $fnFindParent($this);
    }

    public function getGrandParentLevelsAttribute()
    {
        $fnGetParentLevel = function (Category $c, $levels = 0) use (&$fnGetParentLevel): int {
            return $c->parent && $c->parent ?
                $fnGetParentLevel($c->parent, $levels + 1) :
                $levels;
        };

        return $fnGetParentLevel($this);
    }
    //endregion
}
