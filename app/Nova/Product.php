<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\Lenses\BestSellingProducts;
use App\Nova\Filters\ActiveFilter;

class Product extends Resource
{
    public static $model = \App\Models\Product::class;
    public static $title = 'name';
    public static $search = ['name', 'sku', 'barcode'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('SKU')->sortable()->rules('required', 'unique:products,sku,{{resourceId}}'),
            Text::make('Barcode')->rules('nullable', 'unique:products,barcode,{{resourceId}}'),
            BelongsTo::make('Category')->nullable()->searchable(),
            BelongsTo::make('Brand')->nullable()->searchable(),
            BelongsTo::make('Tax Rate', 'taxRate')->nullable()->searchable(),
            Textarea::make('Description')->rows(3),
            Text::make('Unit')->default('piece'),
            Boolean::make('Active')->default(true),
            Boolean::make('Featured')->default(false),
            Boolean::make('Track Inventory')->default(true),
            HasMany::make('Variants', 'variants', ProductVariant::class),
            HasMany::make('Images', 'images', 'ProductImage'),
        ];
    }

    public function lenses(NovaRequest $request)
    {
        return [
            new BestSellingProducts,
        ];
    }

    public function filters(NovaRequest $request)
    {
        return [
            new ActiveFilter,
        ];
    }
}