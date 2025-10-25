<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\CashDrawer;

class CashTransaction extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\CashTransaction>
     */
    public static $model = \App\Models\CashTransaction::class;

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
        'id', 'reference', 'reason',
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

            BelongsTo::make('Cash Drawer', 'cashDrawer', CashDrawer::class)
                ->searchable()
                ->required(),

            Select::make('Type')->options([
                'cash_in' => 'Cash In',
                'cash_out' => 'Cash Out',
                'sale' => 'Sale',
                'refund' => 'Refund',
            ])->displayUsingLabels()
            ->required()
            ->rules('required', 'in:cash_in,cash_out,sale,refund'),

            Number::make('Amount')
                ->step(0.01)
                ->required()
                ->rules('required', 'numeric', 'min:0'),

            Text::make('Reference')
                ->nullable()
                ->hideFromIndex(),

            Text::make('Reason')
                ->nullable(),

            Textarea::make('Notes')
                ->nullable()
                ->hideFromIndex()
                ->rows(3),
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
