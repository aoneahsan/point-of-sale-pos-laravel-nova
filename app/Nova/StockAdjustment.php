<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use App\Nova\Filters\StoreFilter;
use App\Nova\Filters\StatusFilter;

class StockAdjustment extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\StockAdjustment>
     */
    public static $model = \App\Models\StockAdjustment::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'reference';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['reference', 'reason', 'notes'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            // Basic Information
            ID::make()->sortable(),

            BelongsTo::make('Store')
                ->rules('required', 'exists:stores,id')
                ->searchable()
                ->withoutTrashed()
                ->help('The store where this adjustment is being made'),

            Text::make('Reference')
                ->sortable()
                ->readonly()
                ->help('Unique reference number for this stock adjustment')
                ->exceptOnForms(),

            Select::make('Reason')
                ->options([
                    'damaged' => 'Damaged',
                    'theft' => 'Theft',
                    'loss' => 'Loss',
                    'found' => 'Found',
                    'recount' => 'Recount',
                    'other' => 'Other',
                ])
                ->displayUsingLabels()
                ->rules('required')
                ->help('Reason for this stock adjustment'),

            Select::make('Status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->displayUsingLabels()
                ->default('pending')
                ->rules('required')
                ->help('Approval status of this adjustment')
                ->readonly(function ($request) {
                    // Allow status changes only for users with approval permission
                    return !$request->user()->can('approve-stock-adjustments');
                }),

            Text::make('Status Badge', function () {
                $badges = [
                    'pending' => '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-semibold uppercase">Pending</span>',
                    'approved' => '<span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold uppercase">Approved</span>',
                    'rejected' => '<span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold uppercase">Rejected</span>',
                ];
                return $badges[$this->status] ?? $this->status;
            })->asHtml()->exceptOnForms(),

            Textarea::make('Notes')
                ->rows(3)
                ->nullable()
                ->rules('nullable', 'max:1000')
                ->help('Additional notes or details about this adjustment'),

            // User Information
            Panel::make('User Information', [
                BelongsTo::make('Created By', 'user', User::class)
                    ->readonly()
                    ->help('User who created this adjustment')
                    ->exceptOnForms(),

                BelongsTo::make('Approved By', 'approver', User::class)
                    ->nullable()
                    ->readonly()
                    ->help('User who approved/rejected this adjustment')
                    ->exceptOnForms(),

                DateTime::make('Approved At', 'approved_at')
                    ->nullable()
                    ->readonly()
                    ->help('When this adjustment was approved/rejected')
                    ->exceptOnForms(),
            ]),

            // Timestamps
            Panel::make('Timestamps', [
                DateTime::make('Created At')
                    ->readonly()
                    ->exceptOnForms(),

                DateTime::make('Updated At')
                    ->readonly()
                    ->exceptOnForms()
                    ->hideFromIndex(),
            ]),

            // Relationships
            HasMany::make('Items', 'items', StockAdjustmentItem::class),
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
        return [
            new StoreFilter,
            new StatusFilter,
        ];
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
