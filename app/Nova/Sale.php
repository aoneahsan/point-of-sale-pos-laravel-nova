<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\Sale as SaleModel;
use App\Nova\Filters\StoreFilter;
use App\Nova\Filters\StatusFilter;
use App\Nova\Actions\ExportSales;
use App\Nova\Actions\RefundSale;

class Sale extends Resource
{
    /**
     * The model the resource corresponds to.
     */
    public static $model = \App\Models\Sale::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     */
    public static $title = 'reference';

    /**
     * The columns that should be searched.
     */
    public static $search = ['reference', 'id'];

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Reference')
                ->readonly()
                ->sortable()
                ->help('Unique sale reference number'),

            BelongsTo::make('Store')
                ->searchable()
                ->withoutTrashed()
                ->help('Store where this sale was made'),

            BelongsTo::make('User', 'user', User::class)
                ->searchable()
                ->withoutTrashed()
                ->help('Cashier/user who processed this sale')
                ->displayUsing(fn ($user) => $user->name ?? 'N/A'),

            BelongsTo::make('Customer')
                ->nullable()
                ->searchable()
                ->withoutTrashed()
                ->help('Customer associated with this sale (optional)')
                ->hideFromIndex(),

            // Status badge with color coding
            Badge::make('Status')->map([
                SaleModel::STATUS_COMPLETED => 'success',
                SaleModel::STATUS_PENDING => 'warning',
                SaleModel::STATUS_ON_HOLD => 'info',
                SaleModel::STATUS_CANCELLED => 'danger',
                SaleModel::STATUS_REFUNDED => 'danger',
            ])->sortable(),

            // Financial fields with proper formatting
            Number::make('Subtotal')
                ->step(0.01)
                ->displayUsing(fn ($value) => '$' . number_format($value, 2))
                ->readonly()
                ->sortable()
                ->help('Total before tax and discount'),

            Number::make('Tax')
                ->step(0.01)
                ->displayUsing(fn ($value) => '$' . number_format($value, 2))
                ->readonly()
                ->help('Total tax amount')
                ->hideFromIndex(),

            Number::make('Discount')
                ->step(0.01)
                ->displayUsing(fn ($value) => '$' . number_format($value, 2))
                ->readonly()
                ->help('Total discount applied')
                ->hideFromIndex(),

            Number::make('Total')
                ->step(0.01)
                ->displayUsing(fn ($value) => '$' . number_format($value, 2))
                ->readonly()
                ->sortable()
                ->help('Final total amount'),

            // Item count
            Text::make('Items Count', function () {
                return $this->items()->count() . ' item(s)';
            })->onlyOnIndex(),

            // Payment status indicator
            Text::make('Payment Status', function () {
                $totalPaid = $this->payments()->sum('amount');
                if ($totalPaid >= $this->total) {
                    return '<span class="text-green-600 font-semibold">Paid</span>';
                } elseif ($totalPaid > 0) {
                    return '<span class="text-yellow-600 font-semibold">Partially Paid</span>';
                }
                return '<span class="text-red-600 font-semibold">Unpaid</span>';
            })->asHtml()->onlyOnDetail(),

            Textarea::make('Notes')
                ->rows(3)
                ->nullable()
                ->help('Additional notes about this sale')
                ->hideFromIndex(),

            DateTime::make('Created At')
                ->readonly()
                ->sortable()
                ->help('Date and time of sale')
                ->displayUsing(fn ($value) => $value ? $value->format('M d, Y h:i A') : 'N/A'),

            DateTime::make('Updated At')
                ->readonly()
                ->help('Last update timestamp')
                ->hideFromIndex()
                ->onlyOnDetail(),

            // Relationships
            HasMany::make('Items', 'items', SaleItem::class)
                ->help('Individual line items in this sale'),

            HasMany::make('Payments', 'payments', SalePayment::class)
                ->help('Payment transactions for this sale'),

            HasMany::make('Returns', 'returns', SaleReturn::class)
                ->help('Return/refund records for this sale'),
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
    public function filters(NovaRequest $request)
    {
        return [
            new StoreFilter,
            new StatusFilter,
        ];
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
    public function actions(NovaRequest $request)
    {
        return [
            new ExportSales,
            new RefundSale,
        ];
    }
}