<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class SaleItem extends Resource
{
    public static $model = \App\Models\SaleItem::class;
    public static $title = 'id';
    public static $search = [];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Sale')->searchable(),
            BelongsTo::make('Product Variant', 'variant', ProductVariant::class)->searchable(),
            Text::make('Product Name')->readonly(),
            Number::make('Quantity')->min(1)->rules('required', 'integer'),
            Number::make('Price')->step(0.01)->readonly(),
            Number::make('Discount')->step(0.01)->default(0),
            Number::make('Tax')->step(0.01)->default(0),
            Number::make('Subtotal')->step(0.01)->readonly(),
        ];
    }
}