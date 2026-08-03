<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    protected $guarded;

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeGenders(Builder $q): Builder
    {
        return $q->where('depth', 'gender');
    }

    public function scopeCategories(Builder $q): Builder
    {
        return $q->where('depth', 'category');
    }

    public function scopeSubcategories(Builder $q): Builder
    {
        return $q->where('depth', 'subcategory');
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function getDescendantIds(): array
    {
        $ids = [$this->id];

        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getDescendantIds());
        }

        return $ids;
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('nav-genders'));
        static::deleted(fn () => Cache::forget('nav-genders'));
    }

    public function getFullPathAttribute(): string
    {
        $ancestors = collect([]);
        $current = $this;

        // Traverse up the parent chain
        while ($current) {
            $ancestors->prepend($current->name);
            $current = $current->parent;
        }

        return $ancestors->implode(' / ');
    }
}
