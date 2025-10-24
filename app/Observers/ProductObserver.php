<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductObserver
{
    public function creating(Product $product): void
    {
        if (!$product->slug) {
            $product->slug = Str::slug($product->name);
        }
    }

    public function updating(Product $product): void
    {
        if ($product->isDirty('name') && !$product->isDirty('slug')) {
            $product->slug = Str::slug($product->name);
        }
    }
}