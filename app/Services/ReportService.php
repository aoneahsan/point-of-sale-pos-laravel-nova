<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getSalesReport(int $storeId, Carbon $startDate, Carbon $endDate): array
    {
        $sales = Sale::where('store_id', $storeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->completed()
            ->get();

        return [
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total'),
            'total_tax' => $sales->sum('tax'),
            'total_discount' => $sales->sum('discount'),
            'average_sale' => $sales->avg('total'),
            'sales_by_day' => $sales->groupBy(fn($sale) => $sale->created_at->format('Y-m-d'))->map->count(),
        ];
    }

    public function getTopSellingProducts(int $storeId, int $limit = 10)
    {
        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('product_variants', 'sale_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('sales.store_id', $storeId)
            ->where('sales.status', Sale::STATUS_COMPLETED)
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }

    public function getInventoryReport(int $storeId): array
    {
        $variants = ProductVariant::where('store_id', $storeId)->with('product')->get();

        return [
            'total_products' => $variants->count(),
            'total_stock_value' => $variants->sum(fn($v) => $v->stock * $v->cost),
            'low_stock_count' => $variants->filter->isLowStock()->count(),
            'out_of_stock_count' => $variants->where('stock', 0)->count(),
        ];
    }
}