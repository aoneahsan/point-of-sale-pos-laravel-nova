<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\JSON;
use Laravel\Nova\Http\Requests\NovaRequest;

class PaymentMethod extends Resource
{
    public static $model = \App\Models\PaymentMethod::class;
    public static $title = 'name';
    public static $search = ['name', 'code'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable()->rules('required', 'max:255'),
            Text::make('Code')->sortable()->rules('required', 'unique:payment_methods,code,{{resourceId}}'),
            Select::make('Type')->options([
                'cash' => 'Cash',
                'card' => 'Card',
                'digital' => 'Digital',
                'digital_wallet' => 'Digital Wallet',
                'store_credit' => 'Store Credit',
                'gift_card' => 'Gift Card',
                'bank_transfer' => 'Bank Transfer',
                'check' => 'Check',
                'other' => 'Other',
            ])->default('cash'),
            Boolean::make('Active')->default(true),
            Number::make('Sort Order')->default(0),
            JSON::make('Settings'),
        ];
    }
}