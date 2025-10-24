<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class Category extends Resource
{
    public static $model = \App\Models\Category::class;
    public static $title = 'name';
    public static $search = ['name', 'slug'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Slug')->sortable()->rules('required', 'max:255', 'unique:categories,slug,{{resourceId}}')->hideWhenCreating()->hideWhenUpdating(),
            BelongsTo::make('Parent', 'parent', Category::class)->nullable()->searchable(),
            Textarea::make('Description')->rows(3),
            Number::make('Sort Order')->default(0),
            Boolean::make('Active')->default(true),
            HasMany::make('Products'),
            HasMany::make('Children', 'children', Category::class),
        ];
    }
}