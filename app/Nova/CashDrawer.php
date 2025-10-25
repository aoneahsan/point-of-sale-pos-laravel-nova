<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class CashDrawer extends Resource
{
    public static $model = \App\Models\CashDrawer::class;
    public static $title = 'id';
    public static $search = [];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Store')->searchable(),
            BelongsTo::make('User')->searchable(),
            Number::make('Opening Balance')->step(0.01)->readonly(),
            Number::make('Expected Balance')->step(0.01)->readonly(),
            Number::make('Actual Balance')->step(0.01),
            Number::make('Difference')->step(0.01)->readonly(),
            Select::make('Status')->options([
                'open' => 'Open',
                'closed' => 'Closed',
            ])->default('open'),
            DateTime::make('Opened At')->readonly(),
            DateTime::make('Closed At')->nullable()->readonly(),
            HasMany::make('Transactions', 'transactions', CashTransaction::class),
        ];
    }
}