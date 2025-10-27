<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'items']);
        
        if ($request->has('store_id')) {
            $query->where('store_id', $request->store_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }
        
        $sales = $query->latest()->paginate($request->get('per_page', 15));
        
        return SaleResource::collection($sales);
    }

    public function show(Sale $sale)
    {
        return new SaleResource($sale->load(['customer', 'items', 'payments']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'customer_id' => 'nullable|exists:customers,id',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable',
            'items' => 'required|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'payments' => 'required|array',
            'payments.*.payment_method_id' => 'required|exists:payment_methods,id',
            'payments.*.amount' => 'required|numeric|min:0',
        ]);

        // Validate that each item has either product_id or product_variant_id
        foreach ($validated['items'] as $index => $item) {
            if (empty($item['product_id']) && empty($item['product_variant_id'])) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => [
                        "items.{$index}" => ['Each item must have either product_id or product_variant_id'],
                    ],
                ], 422);
            }
        }

        // Calculate expected sale total from items
        $expectedTotal = 0;
        foreach ($validated['items'] as $item) {
            $price = $item['price'] ?? 0;
            $quantity = $item['quantity'];
            $itemDiscount = $item['discount'] ?? 0;
            $expectedTotal += ($price * $quantity) - $itemDiscount;
        }

        // Apply sale-level discount
        $expectedTotal -= ($validated['discount'] ?? 0);

        // Calculate payment total
        $paymentTotal = collect($validated['payments'])->sum('amount');

        // Validate payment total matches expected total
        if (abs($paymentTotal - $expectedTotal) > 0.01) { // Allow 1 cent rounding difference
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'payments' => ['Payment total must match sale total'],
                ],
            ], 422);
        }

        $sale = $this->saleService->createSale([
            'store_id' => $validated['store_id'],
            'user_id' => Auth::id(),
            'customer_id' => $validated['customer_id'] ?? null,
            'discount' => $validated['discount'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'items' => $validated['items'],
        ]);

        $this->saleService->completeSale($sale, $validated['payments']);

        return (new SaleResource($sale->fresh(['customer', 'items', 'payments'])))
            ->response()
            ->setStatusCode(201);
    }

    public function refund(Request $request, Sale $sale)
    {
        // Authorize
        if (!$request->user()->can('process-refunds')) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'reason' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.reason' => 'required|in:customer_request,defective,damaged,incorrect',
        ]);

        // Validate quantities don't exceed original purchase
        foreach ($validated['items'] as $item) {
            $saleItem = \App\Models\SaleItem::findOrFail($item['sale_item_id']);

            // Calculate already returned quantity
            $alreadyReturned = \App\Models\SaleReturnItem::where('sale_item_id', $saleItem->id)->sum('quantity');
            $remainingQuantity = $saleItem->quantity - $alreadyReturned;

            if ($item['quantity'] > $remainingQuantity) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => [
                        'items' => ['Cannot refund more than purchased'],
                    ],
                ], 400);
            }
        }

        return DB::transaction(function () use ($validated, $sale, $request) {
            // Calculate totals for return
            $subtotal = 0;
            $tax = 0;
            foreach ($validated['items'] as $itemData) {
                $saleItem = \App\Models\SaleItem::findOrFail($itemData['sale_item_id']);
                $itemSubtotal = $saleItem->unit_price * $itemData['quantity'];
                $itemTax = ($saleItem->tax / $saleItem->quantity) * $itemData['quantity'];
                $subtotal += $itemSubtotal;
                $tax += $itemTax;
            }
            $total = $subtotal + $tax;

            // Create return record
            $return = \App\Models\SaleReturn::create([
                'sale_id' => $sale->id,
                'store_id' => $sale->store_id,
                'user_id' => $request->user()->id,
                'reference' => 'RET-' . strtoupper(uniqid()),
                'reason' => $validated['reason'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => 'approved',
            ]);

            // Create return items and restore inventory
            foreach ($validated['items'] as $itemData) {
                $saleItem = \App\Models\SaleItem::with('product')->findOrFail($itemData['sale_item_id']);
                $itemTotal = $saleItem->unit_price * $itemData['quantity'];

                // Create return item
                \App\Models\SaleReturnItem::create([
                    'return_id' => $return->id,
                    'sale_item_id' => $saleItem->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $saleItem->unit_price,
                    'total' => $itemTotal,
                ]);

                // Restore inventory
                if ($saleItem->product && $saleItem->product->track_stock) {
                    // Use the product model directly to ensure the increment is applied
                    $product = \App\Models\Product::find($saleItem->product_id);
                    if ($product && $product->track_stock) {
                        $product->increment('stock_quantity', $itemData['quantity']);
                    }
                }
            }

            return response()->json([
                'message' => 'Refund processed successfully',
                'return' => $return->load('items'),
            ]);
        });
    }
}