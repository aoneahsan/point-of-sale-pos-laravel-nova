<?php

namespace App\Livewire\POS;

use Livewire\Component;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class ProductSearch extends Component
{
    public $search = '';
    public $results = [];

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $storeId = Auth::user()->store_id;
            
            $this->results = ProductVariant::with('product')
                ->where('store_id', $storeId)
                ->where(function($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('sku', 'like', "%{$this->search}%")
                        ->orWhere('barcode', 'like', "%{$this->search}%")
                        ->orWhereHas('product', function($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        });
                })
                ->limit(10)
                ->get();
        } else {
            $this->results = [];
        }
    }

    public function selectProduct($variantId)
    {
        $this->dispatch('productAdded', $variantId);
        $this->search = '';
        $this->results = [];
    }

    public function render()
    {
        return view('livewire.p-o-s.product-search');
    }
}