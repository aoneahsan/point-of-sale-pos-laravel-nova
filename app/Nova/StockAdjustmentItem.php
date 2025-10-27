<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class StockAdjustmentItem extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\StockAdjustmentItem>
     */
    public static $model = \App\Models\StockAdjustmentItem::class;

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
    public static $search = [];

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

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

            BelongsTo::make('Stock Adjustment', 'stockAdjustment', StockAdjustment::class)
                ->searchable()
                ->readonly(),

            BelongsTo::make('Product Variant', 'productVariant', ProductVariant::class)
                ->searchable()
                ->readonly()
                ->help('The product variant being adjusted'),

            Text::make('Product', function () {
                if ($this->productVariant && $this->productVariant->product) {
                    return $this->productVariant->product->name . ' - ' . $this->productVariant->name;
                }
                return 'N/A';
            })->readonly()->onlyOnDetail(),

            Number::make('Quantity Before', 'quantity_before')
                ->readonly()
                ->help('Stock quantity before the adjustment')
                ->displayUsing(fn ($value) => number_format($value)),

            Number::make('Quantity After', 'quantity_after')
                ->readonly()
                ->help('Stock quantity after the adjustment')
                ->displayUsing(fn ($value) => number_format($value)),

            Number::make('Difference')
                ->readonly()
                ->help('Change in quantity (positive = added, negative = removed)')
                ->displayUsing(function ($value) {
                    $sign = $value >= 0 ? '+' : '';
                    $color = $value >= 0 ? 'text-green-600' : 'text-red-600';
                    return '<span class="' . $color . ' font-semibold">' . $sign . number_format($value) . '</span>';
                })
                ->asHtml(),

            Text::make('Change Summary', function () {
                $sign = $this->difference >= 0 ? '+' : '';
                $color = $this->difference >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                return '<div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">Before:</span>
                        <span class="font-semibold">' . number_format($this->quantity_before) . '</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">Change:</span>
                        <span class="px-2 py-1 rounded text-xs font-semibold ' . $color . '">' . $sign . number_format($this->difference) . '</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">After:</span>
                        <span class="font-bold">' . number_format($this->quantity_after) . '</span>
                    </div>
                </div>';
            })->asHtml()->onlyOnDetail(),
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
