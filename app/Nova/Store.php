<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\JSON;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class Store extends Resource
{
    public static $model = \App\Models\Store::class;
    public static $title = 'name';
    public static $search = ['name', 'code', 'email'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Code')->sortable()->rules('required', 'max:50', 'unique:stores,code,{{resourceId}}'),
            Textarea::make('Address')->rows(2),
            Text::make('Phone'),
            Text::make('Email')->rules('nullable', 'email'),
            Text::make('Tax Number'),
            Boolean::make('Active')->default(true),
            JSON::make('Settings'),
            HasMany::make('Users'),
            HasMany::make('Product Variants', 'productVariants'),
            HasMany::make('Sales'),
        ];
    }
}