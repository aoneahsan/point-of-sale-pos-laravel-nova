<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class Customer extends Resource
{
    public static $model = \App\Models\Customer::class;
    public static $title = 'name';
    public static $search = ['name', 'email', 'phone', 'code'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Store')->searchable(),
            BelongsTo::make('Customer Group', 'group')->searchable()->nullable(),
            Text::make('Code')->sortable()->rules('required', 'unique:customers,code,{{resourceId}}'),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Email')->rules('nullable', 'email'),
            Text::make('Phone'),
            Textarea::make('Address')->rows(2),
            Text::make('Tax Number'),
            Number::make('Loyalty Points')->default(0)->readonly(),
            Number::make('Store Credit')->step(0.01)->default(0)->readonly(),
            Date::make('Date of Birth', 'date_of_birth'),
            Textarea::make('Notes')->rows(3),
            Boolean::make('Active')->default(true),
            HasMany::make('Sales'),
        ];
    }
}