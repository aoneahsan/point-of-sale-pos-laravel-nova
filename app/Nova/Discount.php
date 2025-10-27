<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use App\Nova\Filters\ActiveFilter;

class Discount extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Discount>
     */
    public static $model = \App\Models\Discount::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['name', 'type'];

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

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('Descriptive name for this discount (e.g., "Summer Sale", "10% Off")'),

            Select::make('Type')
                ->options([
                    'percentage' => 'Percentage',
                    'fixed' => 'Fixed Amount',
                    'buy_x_get_y' => 'Buy X Get Y',
                    'bundle' => 'Bundle Pricing',
                ])
                ->displayUsingLabels()
                ->rules('required')
                ->help('Type of discount calculation'),

            Number::make('Value')
                ->step(0.01)
                ->rules('required', 'numeric', 'min:0')
                ->help('Discount value (percentage or fixed amount depending on type)')
                ->displayUsing(function ($value) {
                    if ($this->type === 'percentage') {
                        return $value . '%';
                    }
                    return '$' . number_format($value, 2);
                }),

            // Conditions & Limits
            Panel::make('Conditions & Limits', [
                Number::make('Minimum Amount', 'min_amount')
                    ->step(0.01)
                    ->nullable()
                    ->rules('nullable', 'numeric', 'min:0')
                    ->help('Minimum purchase amount required to use this discount')
                    ->displayUsing(fn ($value) => $value ? '$' . number_format($value, 2) : 'No minimum'),

                Number::make('Maximum Uses', 'max_uses')
                    ->nullable()
                    ->rules('nullable', 'integer', 'min:1')
                    ->help('Maximum number of times this discount can be used (leave empty for unlimited)'),

                Number::make('Current Uses', 'uses')
                    ->default(0)
                    ->readonly()
                    ->help('Number of times this discount has been used')
                    ->exceptOnForms(),

                Text::make('Usage Status', function () {
                    if ($this->max_uses === null) {
                        return '<span class="text-green-600 font-semibold">' . $this->uses . ' / Unlimited</span>';
                    }
                    $percentage = ($this->uses / $this->max_uses) * 100;
                    $color = $percentage >= 90 ? 'text-red-600' : ($percentage >= 75 ? 'text-yellow-600' : 'text-green-600');
                    return '<span class="' . $color . ' font-semibold">' . $this->uses . ' / ' . $this->max_uses . '</span>';
                })->asHtml()->onlyOnDetail(),

                Textarea::make('Conditions')
                    ->rows(3)
                    ->nullable()
                    ->rules('nullable', 'json')
                    ->help('Additional conditions in JSON format (e.g., specific products, categories, customer groups)')
                    ->hideFromIndex(),
            ]),

            // Time Restrictions
            Panel::make('Time Restrictions', [
                DateTime::make('Start Date', 'start_date')
                    ->nullable()
                    ->rules('nullable', 'date')
                    ->help('When this discount becomes active (leave empty for immediate activation)'),

                DateTime::make('End Date', 'end_date')
                    ->nullable()
                    ->rules('nullable', 'date', 'after_or_equal:start_date')
                    ->help('When this discount expires (leave empty for no expiration)'),

                Text::make('Status', function () {
                    $now = now();

                    if (!$this->active) {
                        return '<span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs font-semibold">INACTIVE</span>';
                    }

                    $started = $this->start_date ? $this->start_date <= $now : true;
                    $notExpired = $this->end_date ? $this->end_date >= $now : true;

                    if ($started && $notExpired) {
                        return '<span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">ACTIVE</span>';
                    } elseif (!$started) {
                        return '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-semibold">SCHEDULED</span>';
                    } else {
                        return '<span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold">EXPIRED</span>';
                    }
                })->asHtml()->exceptOnForms(),
            ]),

            // Status
            Boolean::make('Active')
                ->default(true)
                ->help('Only active discounts can be applied to sales'),

            // Relationships
            HasMany::make('Coupons', 'coupons', Coupon::class),
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
            new ActiveFilter,
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
