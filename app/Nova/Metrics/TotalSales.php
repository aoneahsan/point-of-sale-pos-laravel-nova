<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\Sale;

class TotalSales extends Trend
{
    public function name()
    {
        return 'Total Sales';
    }

    public function calculate(NovaRequest $request)
    {
        return $this->sumByDays($request, Sale::where('status', 'completed'), 'total');
    }

    public function ranges()
    {
        return [
            7 => '7 Days',
            30 => '30 Days',
            60 => '60 Days',
            90 => '90 Days',
        ];
    }
}