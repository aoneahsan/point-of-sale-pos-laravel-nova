<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Http\Requests\NovaRequest;

class TaxRate extends Resource
{
    public static $model = \App\Models\TaxRate::class;
    public static $title = 'name';
    public static $search = ['name', 'code'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Code')->sortable()->rules('required', 'unique:tax_rates,code,{{resourceId}}'),
            Number::make('Rate')->min(0)->max(100)->step(0.01)->rules('required'),
            Boolean::make('Active')->default(true),
            Boolean::make('Is Default')->default(false),
            Textarea::make('Description')->rows(3),
        ];
    }
}