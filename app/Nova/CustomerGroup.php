<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class CustomerGroup extends Resource
{
    public static $model = \App\Models\CustomerGroup::class;
    public static $title = 'name';
    public static $search = ['name', 'code'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Code')->sortable()->rules('required', 'unique:customer_groups,code,{{resourceId}}'),
            Number::make('Discount Percentage')->min(0)->max(100)->step(0.01)->default(0),
            Number::make('Loyalty Points Multiplier')->min(0)->step(0.1)->default(1.0),
            Textarea::make('Description')->rows(3),
            Boolean::make('Active')->default(true),
            HasMany::make('Customers'),
        ];
    }
}