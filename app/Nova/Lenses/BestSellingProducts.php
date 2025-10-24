<?php

namespace App\Nova\Lenses;

use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Illuminate\Support\Facades\DB;

class BestSellingProducts extends Lens
{
    public function name()
    {
        return 'Best Selling Products';
    }

    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->select('products.*')
                  ->selectRaw('SUM(sale_items.quantity) as total_sold')
                  ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                  ->join('sale_items', 'product_variants.id', '=', 'sale_items.product_variant_id')
                  ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                  ->where('sales.status', 'completed')
                  ->groupBy('products.id')
                  ->orderByDesc('total_sold')
        ));
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable(),
            Number::make('Total Sold', 'total_sold')->sortable(),
        ];
    }
}