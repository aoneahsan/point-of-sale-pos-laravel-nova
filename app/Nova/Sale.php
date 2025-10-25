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
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\Sale as SaleModel;
use App\Nova\Filters\StoreFilter;
use App\Nova\Filters\StatusFilter;
use App\Nova\Actions\ExportSales;
use App\Nova\Actions\RefundSale;

class Sale extends Resource
{
    public static $model = \App\Models\Sale::class;
    public static $title = 'reference';
    public static $search = ['reference'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Store')->searchable(),
            BelongsTo::make('User')->searchable(),
            BelongsTo::make('Customer')->nullable()->searchable(),
            Text::make('Reference')->readonly(),
            Number::make('Subtotal')->step(0.01)->readonly(),
            Number::make('Tax')->step(0.01)->readonly(),
            Number::make('Discount')->step(0.01)->readonly(),
            Number::make('Total')->step(0.01)->readonly(),
            Select::make('Status')->options([
                SaleModel::STATUS_COMPLETED => 'Completed',
                SaleModel::STATUS_PENDING => 'Pending',
                SaleModel::STATUS_ON_HOLD => 'On Hold',
                SaleModel::STATUS_CANCELLED => 'Cancelled',
                SaleModel::STATUS_REFUNDED => 'Refunded',
            ])->default(SaleModel::STATUS_COMPLETED),
            Textarea::make('Notes')->rows(3),
            DateTime::make('Created At')->readonly(),
            HasMany::make('Items', 'items', SaleItem::class),
            HasMany::make('Payments', 'payments', SalePayment::class),
        ];
    }

    public function filters(NovaRequest $request)
    {
        return [
            new StoreFilter,
            new StatusFilter,
        ];
    }

    public function actions(NovaRequest $request)
    {
        return [
            new ExportSales,
            new RefundSale,
        ];
    }
}