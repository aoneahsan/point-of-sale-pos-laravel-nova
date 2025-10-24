<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class Brand extends Resource
{
    public static $model = \App\Models\Brand::class;
    public static $title = 'name';
    public static $search = ['name', 'slug'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Slug')->sortable()->rules('required', 'max:255', 'unique:brands,slug,{{resourceId}}')->hideWhenCreating()->hideWhenUpdating(),
            Textarea::make('Description')->rows(3),
            Image::make('Logo')->disk('public'),
            Boolean::make('Active')->default(true),
            HasMany::make('Products'),
        ];
    }
}