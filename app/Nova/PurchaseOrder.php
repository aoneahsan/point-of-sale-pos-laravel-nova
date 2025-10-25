<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class PurchaseOrder extends Resource
{
    public static $model = \App\Models\PurchaseOrder::class;
    public static $title = 'reference';
    public static $search = ['reference'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Store')->searchable(),
            BelongsTo::make('Supplier')->searchable(),
            BelongsTo::make('User')->searchable(),
            Text::make('Reference')->readonly(),
            Date::make('Order Date')->default(now()),
            Date::make('Expected Date'),
            Date::make('Received Date')->nullable(),
            Number::make('Subtotal')->step(0.01)->readonly(),
            Number::make('Tax')->step(0.01)->readonly(),
            Number::make('Total')->step(0.01)->readonly(),
            Select::make('Status')->options([
                'draft' => 'Draft',
                'pending' => 'Pending',
                'ordered' => 'Ordered',
                'received' => 'Received',
                'cancelled' => 'Cancelled',
            ])->default('draft'),
            Textarea::make('Notes')->rows(3),
            HasMany::make('Items', 'items', PurchaseOrderItem::class),
        ];
    }
}