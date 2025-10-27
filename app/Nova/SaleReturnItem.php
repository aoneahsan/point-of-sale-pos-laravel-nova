<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class SaleReturnItem extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\SaleReturnItem>
     */
    public static $model = \App\Models\SaleReturnItem::class;

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
     * Get the label for the resource.
     *
     * @return string
     */
    public static function label()
    {
        return 'Return Items';
    }

    /**
     * Get the singular label for the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return 'Return Item';
    }

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

            BelongsTo::make('Return', 'return', SaleReturn::class)
                ->searchable()
                ->readonly()
                ->help('The return this item belongs to')
                ->displayUsing(fn ($return) => $return->reference ?? 'N/A'),

            BelongsTo::make('Sale Item', 'saleItem', SaleItem::class)
                ->searchable()
                ->readonly()
                ->help('The original sale item being returned'),

            Text::make('Product', function () {
                if ($this->saleItem) {
                    if ($this->saleItem->productVariant) {
                        return $this->saleItem->productVariant->product->name . ' - ' . $this->saleItem->productVariant->name;
                    } elseif ($this->saleItem->product) {
                        return $this->saleItem->product->name;
                    }
                }
                return 'N/A';
            })->readonly()->onlyOnDetail(),

            Number::make('Quantity')
                ->min(1)
                ->rules('required', 'integer', 'min:1')
                ->readonly()
                ->help('Quantity being returned')
                ->displayUsing(fn ($value) => number_format($value)),

            Currency::make('Unit Price', 'unit_price')
                ->step(0.01)
                ->readonly()
                ->help('Price per unit at time of original sale')
                ->displayUsing(fn ($value) => '$' . number_format($value, 2)),

            Currency::make('Total')
                ->step(0.01)
                ->readonly()
                ->help('Total refund for this line item (quantity × unit price)')
                ->displayUsing(fn ($value) => '$' . number_format($value, 2)),

            Text::make('Line Total', function () {
                return '<div class="space-y-1">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Quantity:</span>
                        <span class="font-semibold">' . number_format($this->quantity) . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Unit Price:</span>
                        <span class="font-semibold">$' . number_format($this->unit_price, 2) . '</span>
                    </div>
                    <div class="flex justify-between border-t pt-1">
                        <span class="text-gray-800 font-semibold">Line Total:</span>
                        <span class="font-bold text-lg">$' . number_format($this->total, 2) . '</span>
                    </div>
                </div>';
            })->asHtml()->onlyOnDetail(),

            Text::make('Original Sale', function () {
                if ($this->saleItem && $this->saleItem->sale) {
                    return '<a href="/admin/resources/sales/' . $this->saleItem->sale->id . '"
                           class="link-default font-semibold">'
                           . $this->saleItem->sale->reference . '</a>';
                }
                return 'N/A';
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
