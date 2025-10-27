<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use App\Nova\Filters\StoreFilter;
use App\Nova\Filters\StatusFilter;

class SaleReturn extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\SaleReturn>
     */
    public static $model = \App\Models\SaleReturn::class;

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
     * Get the label for the resource.
     *
     * @return string
     */
    public static function label()
    {
        return 'Returns';
    }

    /**
     * Get the singular label for the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return 'Return';
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
            // Basic Information
            ID::make()->sortable(),

            BelongsTo::make('Store')
                ->rules('required', 'exists:stores,id')
                ->searchable()
                ->withoutTrashed()
                ->help('The store where this return is being processed'),

            BelongsTo::make('Sale', 'sale', Sale::class)
                ->rules('required', 'exists:sales,id')
                ->searchable()
                ->help('The original sale being returned')
                ->displayUsing(fn ($sale) => $sale->reference ?? 'N/A'),

            Text::make('Reference')
                ->sortable()
                ->readonly()
                ->help('Unique reference number for this return')
                ->exceptOnForms(),

            Text::make('Reason')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Reason for this return (e.g., "Defective product", "Wrong item", "Customer changed mind")'),

            Select::make('Status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->displayUsingLabels()
                ->default('pending')
                ->rules('required')
                ->help('Approval status of this return')
                ->readonly(function ($request) {
                    // Allow status changes only for users with approval permission
                    return !$request->user()->can('approve-returns');
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
                ->help('Additional notes or details about this return'),

            // Financial Information
            Panel::make('Financial Information', [
                Currency::make('Subtotal')
                    ->rules('required', 'numeric', 'min:0')
                    ->step(0.01)
                    ->readonly()
                    ->help('Subtotal amount being returned (excluding tax)')
                    ->displayUsing(fn ($value) => '$' . number_format($value, 2)),

                Currency::make('Tax')
                    ->rules('required', 'numeric', 'min:0')
                    ->step(0.01)
                    ->default(0)
                    ->readonly()
                    ->help('Tax amount being returned')
                    ->displayUsing(fn ($value) => '$' . number_format($value, 2)),

                Currency::make('Total')
                    ->rules('required', 'numeric', 'min:0')
                    ->step(0.01)
                    ->readonly()
                    ->help('Total refund amount (subtotal + tax)')
                    ->displayUsing(fn ($value) => '$' . number_format($value, 2)),

                Text::make('Refund Details', function () {
                    return '<div class="space-y-1">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-semibold">$' . number_format($this->subtotal, 2) . '</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax:</span>
                            <span class="font-semibold">$' . number_format($this->tax, 2) . '</span>
                        </div>
                        <div class="flex justify-between border-t pt-1">
                            <span class="text-gray-800 font-semibold">Total Refund:</span>
                            <span class="font-bold text-lg">$' . number_format($this->total, 2) . '</span>
                        </div>
                    </div>';
                })->asHtml()->onlyOnDetail(),
            ]),

            // User Information
            Panel::make('User Information', [
                BelongsTo::make('Processed By', 'user', User::class)
                    ->readonly()
                    ->help('User who processed this return')
                    ->exceptOnForms(),

                BelongsTo::make('Approved By', 'approver', User::class)
                    ->nullable()
                    ->readonly()
                    ->help('User who approved/rejected this return')
                    ->exceptOnForms(),

                DateTime::make('Approved At', 'approved_at')
                    ->nullable()
                    ->readonly()
                    ->help('When this return was approved/rejected')
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
            HasMany::make('Items', 'items', SaleReturnItem::class),
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
