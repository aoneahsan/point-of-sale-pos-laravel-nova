<?php

namespace App\Livewire\POS;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductSearch extends Component
{
    public $search = '';
    public $results = [];

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $storeId = Auth::user()->store_id;

            // Search products by name, SKU, or barcode
            $this->results = Product::with(['category', 'brand'])
                ->where('store_id', $storeId)
                ->where('is_active', true)
                ->where(function($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('sku', 'like', "%{$this->search}%")
                        ->orWhere('barcode', 'like', "%{$this->search}%");
                })
                ->limit(10)
                ->get()
                ->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'barcode' => $product->barcode,
                        'price' => $product->price,
                        'stock_quantity' => $product->stock_quantity,
                        'track_stock' => $product->track_stock,
                        'is_low_stock' => $product->isLowStock(),
                        'category_name' => $product->category?->name,
                        'brand_name' => $product->brand?->name,
                    ];
                });
        } else {
            $this->results = [];
        }
    }

    public function selectProduct($productId)
    {
        $this->dispatch('productAdded', $productId);
        $this->search = '';
        $this->results = [];
    }

    public function render()
    {
        return view('livewire.p-o-s.product-search');
    }
}