<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class Supplier extends Resource
{
    public static $model = \App\Models\Supplier::class;
    public static $title = 'name';
    public static $search = ['name', 'code', 'email'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Code')->sortable()->rules('required', 'unique:suppliers,code,{{resourceId}}'),
            Text::make('Email')->rules('nullable', 'email'),
            Text::make('Phone'),
            Textarea::make('Address')->rows(2),
            Text::make('Contact Person'),
            Text::make('Tax Number'),
            Text::make('Payment Terms'),
            Textarea::make('Notes')->rows(3),
            Boolean::make('Active')->default(true),
            HasMany::make('Purchase Orders', 'purchaseOrders'),
        ];
    }
}