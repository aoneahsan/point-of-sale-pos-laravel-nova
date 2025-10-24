<?php

namespace App\Livewire\POS;

use Livewire\Component;
use App\Models\Sale;

class Receipt extends Component
{
    public $saleId;
    public $sale;

    public function mount($saleId)
    {
        $this->saleId = $saleId;
        $this->sale = Sale::with(['store', 'customer', 'items.variant.product', 'payments.paymentMethod'])
            ->findOrFail($saleId);
    }

    public function print()
    {
        $this->dispatch('printReceipt');
    }

    public function newSale()
    {
        return redirect()->route('pos.index');
    }

    public function render()
    {
        return view('livewire.p-o-s.receipt');
    }
}