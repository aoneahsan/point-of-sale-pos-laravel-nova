<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'variants']);
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->search($search);
        }
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        
        $products = $query->active()->paginate($request->get('per_page', 15));
        
        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load(['category', 'brand', 'variants']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'sku' => 'required|unique:products',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable',
        ]);

        $product = Product::create($validated);
        return new ProductResource($product);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|max:255',
            'sku' => 'sometimes|unique:products,sku,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
        ]);

        $product->update($validated);
        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}