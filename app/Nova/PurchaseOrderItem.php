<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class PurchaseOrderItem extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\PurchaseOrderItem>
     */
    public static $model = \App\Models\PurchaseOrderItem::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Purchase Order', 'purchaseOrder', PurchaseOrder::class)
                ->searchable()
                ->required(),

            BelongsTo::make('Product Variant', 'productVariant', ProductVariant::class)
                ->searchable()
                ->required(),

            Number::make('Quantity')
                ->min(1)
                ->step(1)
                ->required()
                ->rules('required', 'integer', 'min:1'),

            Number::make('Unit Cost')
                ->step(0.01)
                ->required()
                ->rules('required', 'numeric', 'min:0')
                ->displayUsing(function ($value) {
                    return '$' . number_format($value, 2);
                }),

            Number::make('Total')
                ->step(0.01)
                ->readonly()
                ->displayUsing(function ($value) {
                    return '$' . number_format($value, 2);
                })
                ->help('Calculated as Quantity × Unit Cost'),

            Number::make('Received Quantity')
                ->min(0)
                ->step(1)
                ->default(0)
                ->rules('integer', 'min:0')
                ->help('How many units have been received'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
