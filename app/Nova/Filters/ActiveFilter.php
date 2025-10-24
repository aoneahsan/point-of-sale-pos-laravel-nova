<?php

namespace App\Nova\Filters;

use Laravel\Nova\Filters\BooleanFilter;
use Laravel\Nova\Http\Requests\NovaRequest;

class ActiveFilter extends BooleanFilter
{
    public $name = 'Active Status';

    public function apply(NovaRequest $request, $query, $value)
    {
        if ($value['active']) {
            return $query->where('active', true);
        }
        if ($value['inactive']) {
            return $query->where('active', false);
        }
        return $query;
    }

    public function options(NovaRequest $request)
    {
        return [
            'Active' => 'active',
            'Inactive' => 'inactive',
        ];
    }
}