<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
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
            // Basic Information
            ID::make()->sortable(),

            BelongsTo::make('Store')
                ->rules('required', 'exists:stores,id')
                ->searchable()
                ->withoutTrashed()
                ->help('The store this product belongs to'),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Product name as displayed to customers'),

            Text::make('SKU')
                ->sortable()
                ->rules('required', 'max:100', 'unique:products,sku,{{resourceId}}')
                ->help('Stock Keeping Unit - unique product identifier'),

            Text::make('Barcode')
                ->rules('nullable', 'max:100', 'unique:products,barcode,{{resourceId}}')
                ->help('Product barcode for scanning (EAN, UPC, etc.)'),

            Text::make('Unit')
                ->default('piece')
                ->rules('nullable', 'max:50')
                ->help('Unit of measurement (piece, kg, liter, etc.)'),

            Textarea::make('Description')
                ->rows(3)
                ->rules('nullable')
                ->help('Detailed product description'),

            // Categorization
            Panel::make('Categorization', [
                BelongsTo::make('Category')
                    ->nullable()
                    ->searchable()
                    ->withoutTrashed()
                    ->help('Product category for organization'),

                BelongsTo::make('Brand')
                    ->nullable()
                    ->searchable()
                    ->withoutTrashed()
                    ->help('Product brand/manufacturer'),

                BelongsTo::make('Tax Rate', 'taxRate')
                    ->nullable()
                    ->searchable()
                    ->withoutTrashed()
                    ->help('Tax rate applied to this product'),
            ]),

            // Pricing
            Panel::make('Pricing', [
                Currency::make('Price')
                    ->rules('required', 'numeric', 'min:0')
                    ->step(0.01)
                    ->help('Selling price (excluding tax)')
                    ->displayUsing(fn ($value) => $value ? '$' . number_format($value, 2) : '$0.00'),

                Currency::make('Cost')
                    ->rules('nullable', 'numeric', 'min:0')
                    ->step(0.01)
                    ->help('Product cost price (for profit calculation)')
                    ->displayUsing(fn ($value) => $value ? '$' . number_format($value, 2) : '$0.00'),

                Number::make('Profit Margin %')
                    ->onlyOnDetail()
                    ->resolveUsing(function () {
                        if ($this->price && $this->cost && $this->cost > 0) {
                            return round((($this->price - $this->cost) / $this->cost) * 100, 2);
                        }
                        return 0;
                    })
                    ->help('Calculated: ((Price - Cost) / Cost) × 100'),
            ]),

            // Inventory Management
            Panel::make('Inventory', [
                Boolean::make('Track Stock')
                    ->default(true)
                    ->help('Enable stock quantity tracking for this product'),

                Number::make('Stock Quantity')
                    ->rules('nullable', 'integer', 'min:0')
                    ->min(0)
                    ->step(1)
                    ->default(0)
                    ->help('Current stock quantity on hand')
                    ->displayUsing(fn ($value) => $value ?? 0)
                    ->dependsOn(['track_stock'], function (Number $field, NovaRequest $request, $formData) {
                        if ($formData->track_stock === false) {
                            $field->readonly();
                            $field->help('Stock tracking disabled - quantity not tracked');
                        }
                    }),

                Number::make('Reorder Point')
                    ->rules('nullable', 'integer', 'min:0')
                    ->min(0)
                    ->step(1)
                    ->default(10)
                    ->help('Minimum stock level before reorder alert is triggered')
                    ->dependsOn(['track_stock'], function (Number $field, NovaRequest $request, $formData) {
                        if ($formData->track_stock === false) {
                            $field->readonly();
                            $field->help('Stock tracking disabled - reorder point not applicable');
                        }
                    }),

                Boolean::make('Low Stock', function () {
                    return $this->track_stock && $this->stock_quantity <= $this->reorder_point;
                })
                    ->onlyOnDetail()
                    ->help('Indicates if current stock is at or below reorder point'),
            ]),

            // Status & Settings
            Panel::make('Status & Settings', [
                Boolean::make('Active')
                    ->default(true)
                    ->help('Only active products appear in POS and reports'),

                Boolean::make('Featured')
                    ->default(false)
                    ->help('Featured products appear prominently in POS'),

                Boolean::make('Track Inventory')
                    ->default(true)
                    ->help('Legacy field - use "Track Stock" instead')
                    ->hideFromIndex(),
            ]),

            // Relationships
            HasMany::make('Variants', 'variants', ProductVariant::class),
            HasMany::make('Images', 'images', ProductImage::class),
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