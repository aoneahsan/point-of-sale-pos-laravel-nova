<?php

namespace App\Nova\Filters;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\Store;

class StoreFilter extends Filter
{
    public $name = 'Store';

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('store_id', $value);
    }

    public function options(NovaRequest $request)
    {
        return Store::active()->pluck('id', 'name')->toArray();
    }
}