<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;

class StockMovement extends Resource
{
    public static $model = \App\Models\StockMovement::class;
    public static $title = 'id';
    public static $search = [];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Product Variant', 'variant', ProductVariant::class)->searchable(),
            BelongsTo::make('Store')->searchable(),
            Select::make('Type')->options([
                'in' => 'In',
                'out' => 'Out',
                'adjustment' => 'Adjustment',
                'transfer' => 'Transfer',
            ]),
            Number::make('Quantity')->rules('required', 'integer'),
            Number::make('Quantity Before')->readonly(),
            Number::make('Quantity After')->readonly(),
            Text::make('Reference'),
            MorphTo::make('Related', 'relatable')->types([
                Sale::class,
                PurchaseOrder::class,
            ]),
            DateTime::make('Created At')->readonly(),
        ];
    }
}