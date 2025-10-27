<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $sale = $this->saleService->createSale([
            'store_id' => $validated['store_id'],
            'user_id' => Auth::id(),
            'customer_id' => $validated['customer_id'] ?? null,
            'discount' => $validated['discount'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'items' => $validated['items'],
        ]);

        $this->saleService->completeSale($sale, $validated['payments']);

        return new SaleResource($sale->fresh(['customer', 'items', 'payments']));
    }
}