<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\Sale;

class AverageSale extends Value
{
    public function name()
    {
        return 'Average Sale';
    }

    public function calculate(NovaRequest $request)
    {
        return $this->average($request, Sale::where('status', 'completed'), 'total')
            ->currency('$');
    }

    public function ranges()
    {
        return [
            7 => '7 Days',
            30 => '30 Days',
            60 => '60 Days',
        ];
    }
}