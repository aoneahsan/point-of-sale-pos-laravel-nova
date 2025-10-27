<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use App\Nova\Filters\ActiveFilter;

class Coupon extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Coupon>
     */
    public static $model = \App\Models\Coupon::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'code';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['code'];

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

            BelongsTo::make('Discount')
                ->rules('required', 'exists:discounts,id')
                ->searchable()
                ->withoutTrashed()
                ->help('The discount this coupon code applies'),

            Text::make('Code')
                ->sortable()
                ->rules('required', 'max:255', 'unique:coupons,code,{{resourceId}}')
                ->help('Unique coupon code that customers enter at checkout')
                ->displayUsing(fn ($value) => strtoupper($value))
                ->creationRules('unique:coupons,code')
                ->updateRules('unique:coupons,code,{{resourceId}}'),

            Text::make('Code (Display)', function () {
                return '<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded font-mono text-sm font-bold">'
                    . strtoupper($this->code) . '</span>';
            })->asHtml()->onlyOnDetail(),

            // Usage Limits
            Panel::make('Usage Limits', [
                Number::make('Maximum Uses', 'max_uses')
                    ->nullable()
                    ->rules('nullable', 'integer', 'min:1')
                    ->help('Total number of times this coupon can be used (leave empty for unlimited)'),

                Number::make('Current Uses', 'uses')
                    ->default(0)
                    ->readonly()
                    ->help('Number of times this coupon has been used')
                    ->exceptOnForms(),

                Number::make('Max Uses Per Customer', 'max_uses_per_customer')
                    ->default(1)
                    ->rules('required', 'integer', 'min:1')
                    ->help('Maximum number of times a single customer can use this coupon'),

                Text::make('Usage Status', function () {
                    if ($this->max_uses === null) {
                        return '<span class="text-green-600 font-semibold">' . $this->uses . ' / Unlimited</span>';
                    }

                    $percentage = $this->max_uses > 0 ? ($this->uses / $this->max_uses) * 100 : 0;
                    $remaining = $this->max_uses - $this->uses;

                    if ($remaining <= 0) {
                        return '<span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold">DEPLETED</span>';
                    }

                    $color = $percentage >= 90 ? 'text-red-600' : ($percentage >= 75 ? 'text-yellow-600' : 'text-green-600');
                    return '<span class="' . $color . ' font-semibold">' . $this->uses . ' / ' . $this->max_uses . ' (' . $remaining . ' remaining)</span>';
                })->asHtml()->onlyOnDetail(),
            ]),

            // Expiration
            Panel::make('Expiration', [
                DateTime::make('Expires At', 'expires_at')
                    ->nullable()
                    ->rules('nullable', 'date', 'after:now')
                    ->help('When this coupon expires (leave empty for no expiration)')
                    ->hideFromIndex(),

                Text::make('Expiry Status', function () {
                    if (!$this->expires_at) {
                        return '<span class="text-green-600 font-semibold">Never Expires</span>';
                    }

                    $now = now();
                    $expiresAt = $this->expires_at;

                    if ($expiresAt < $now) {
                        return '<span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold">EXPIRED</span>';
                    }

                    $daysUntilExpiry = $now->diffInDays($expiresAt);

                    if ($daysUntilExpiry <= 7) {
                        return '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-semibold">Expires in ' . $daysUntilExpiry . ' days</span>';
                    }

                    return '<span class="text-green-600 font-semibold">Valid until ' . $expiresAt->format('M d, Y') . '</span>';
                })->asHtml()->exceptOnForms(),
            ]),

            // Status
            Boolean::make('Active')
                ->default(true)
                ->help('Only active coupons can be redeemed'),

            // Overall Status Badge
            Text::make('Overall Status', function () {
                $now = now();

                // Check if inactive
                if (!$this->active) {
                    return '<span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs font-semibold">INACTIVE</span>';
                }

                // Check if expired
                if ($this->expires_at && $this->expires_at < $now) {
                    return '<span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold">EXPIRED</span>';
                }

                // Check if depleted
                if ($this->max_uses !== null && $this->uses >= $this->max_uses) {
                    return '<span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold">DEPLETED</span>';
                }

                // Check if expiring soon (within 7 days)
                if ($this->expires_at && $now->diffInDays($this->expires_at) <= 7) {
                    return '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-semibold">EXPIRING SOON</span>';
                }

                // Otherwise, it's valid
                return '<span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">VALID</span>';
            })->asHtml()->exceptOnForms(),
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
