<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\Lenses\LowStockProducts;
use App\Nova\Filters\StoreFilter;

class ProductVariant extends Resource
{
    public static $model = \App\Models\ProductVariant::class;
    public static $title = 'name';
    public static $search = ['name', 'sku', 'barcode'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Product')->searchable(),
            BelongsTo::make('Store')->searchable(),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('SKU')->sortable()->rules('required', 'unique:product_variants,sku,{{resourceId}}'),
            Text::make('Barcode')->rules('nullable', 'unique:product_variants,barcode,{{resourceId}}'),
            Number::make('Price')->min(0)->step(0.01)->rules('required'),
            Number::make('Cost')->min(0)->step(0.01)->rules('required'),
            Number::make('Stock')->min(0)->rules('required', 'integer'),
            Number::make('Low Stock Threshold')->min(0)->default(5)->rules('required', 'integer'),
            Image::make('Image')->disk('public'),
            KeyValue::make('Attributes')->rules('json'),
        ];
    }

    public function lenses(NovaRequest $request)
    {
        return [
            new LowStockProducts,
        ];
    }

    public function filters(NovaRequest $request)
    {
        return [
            new StoreFilter,
        ];
    }
}