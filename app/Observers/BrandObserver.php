<?php

namespace App\Observers;

use App\Models\Brand;
use Illuminate\Support\Str;

class BrandObserver
{
    public function creating(Brand $brand): void
    {
        if (!$brand->slug) {
            $brand->slug = Str::slug($brand->name);
        }
    }

    public function updating(Brand $brand): void
    {
        if ($brand->isDirty('name') && !$brand->isDirty('slug')) {
            $brand->slug = Str::slug($brand->name);
        }
    }
}