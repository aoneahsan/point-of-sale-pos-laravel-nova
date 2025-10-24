<?php

namespace App\Nova\Filters;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class StatusFilter extends Filter
{
    public $name = 'Status';

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('status', $value);
    }

    public function options(NovaRequest $request)
    {
        return [
            'Completed' => 'completed',
            'Pending' => 'pending',
            'On Hold' => 'on_hold',
            'Cancelled' => 'cancelled',
            'Refunded' => 'refunded',
        ];
    }
}