<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryObserver
{
    public function creating(Category $category): void
    {
        if (!$category->slug) {
            $category->slug = Str::slug($category->name);
        }
    }

    public function updating(Category $category): void
    {
        if ($category->isDirty('name') && !$category->isDirty('slug')) {
            $category->slug = Str::slug($category->name);
        }
    }
}