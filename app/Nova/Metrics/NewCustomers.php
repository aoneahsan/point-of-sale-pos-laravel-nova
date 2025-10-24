<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\Customer;

class NewCustomers extends Value
{
    public function name()
    {
        return 'New Customers';
    }

    public function calculate(NovaRequest $request)
    {
        return $this->count($request, Customer::class);
    }

    public function ranges()
    {
        return [
            7 => '7 Days',
            30 => '30 Days',
            60 => '60 Days',
            365 => '365 Days',
        ];
    }
}