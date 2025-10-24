<?php

namespace App\Nova\Lenses;

use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use App\Models\ProductVariant;

class LowStockProducts extends Lens
{
    public function name()
    {
        return 'Low Stock Products';
    }

    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->whereColumn('stock', '<=', 'low_stock_threshold')
                  ->with('product')
        ));
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Product', function () {
                return $this->product->name;
            }),
            Text::make('Variant', 'name'),
            Number::make('Stock')->sortable(),
            Number::make('Threshold', 'low_stock_threshold'),
        ];
    }
}