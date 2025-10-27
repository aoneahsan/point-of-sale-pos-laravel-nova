<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\ProductVariant;

class SaleItem extends Resource
{
    public static $model = \App\Models\SaleItem::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     */
    public static $search = [];

    /**
     * Get the displayable label of the resource.
     */
    public function title(): string
    {
        if ($this->productVariant) {
            return $this->productVariant->product->name . ' - ' . $this->productVariant->name;
        }
        return $this->product ? $this->product->name : 'Sale Item #' . $this->id;
    }

    /**
     * Indicates if the resource should be displayed in the sidebar.
     */
    public static $displayInNavigation = false;

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Sale')->searchable()->readonly(),

            // Product relationship - shown only if product_id exists (no variant)
            BelongsTo::make('Product')
                ->searchable()
                ->nullable()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->readonly(),

            // Product Variant relationship - shown only if product_variant_id exists
            BelongsTo::make('Product Variant', 'productVariant', ProductVariant::class)
                ->searchable()
                ->nullable()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->readonly(),

            // Display product name (handles both product and variant)
            Text::make('Item Name', function () {
                if ($this->productVariant) {
                    return $this->productVariant->product->name . ' - ' . $this->productVariant->name;
                }
                return $this->product ? $this->product->name : 'N/A';
            })->readonly()
                ->help('Product or product variant sold'),

            Number::make('Quantity')
                ->min(1)
                ->rules('required', 'integer')
                ->readonly()
                ->help('Quantity of items sold'),

            Number::make('Unit Price', 'unit_price')
                ->step(0.01)
                ->displayUsing(fn ($value) => '$' . number_format($value, 2))
                ->readonly()
                ->help('Price per unit at time of sale'),

            Number::make('Unit Cost', 'unit_cost')
                ->step(0.01)
                ->displayUsing(fn ($value) => '$' . number_format($value, 2))
                ->readonly()
                ->help('Cost per unit at time of sale')
                ->hideFromIndex(),

            Number::make('Discount')
                ->step(0.01)
                ->displayUsing(fn ($value) => '$' . number_format($value, 2))
                ->default(0)
                ->readonly()
                ->help('Total discount applied to this item'),

            Number::make('Tax')
                ->step(0.01)
                ->displayUsing(fn ($value) => '$' . number_format($value, 2))
                ->default(0)
                ->readonly()
                ->help('Total tax for this item'),

            Number::make('Total')
                ->step(0.01)
                ->displayUsing(fn ($value) => '$' . number_format($value, 2))
                ->readonly()
                ->help('Final total for this line item'),

            // Calculated profit field
            Text::make('Profit', function () {
                $profit = $this->getProfit();
                $color = $profit >= 0 ? 'text-green-600' : 'text-red-600';
                return '<span class="' . $color . ' font-semibold">$' . number_format($profit, 2) . '</span>';
            })->asHtml()->onlyOnDetail(),
        ];
    }

    /**
     * Get the cards available for the resource.
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }
}