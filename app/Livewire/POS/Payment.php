<?php

namespace App\Livewire\POS;

use Livewire\Component;
use App\Models\PaymentMethod;
use App\Services\SaleService;
use Illuminate\Support\Facades\Auth;

class Payment extends Component
{
    public $cart = [];
    public $total = 0;
    public $customerId = null;
    public $discount = 0;
    public $notes = '';
    public $payments = [];
    public $selectedPaymentMethod = null;
    public $paymentAmount = 0;

    public function mount($cart, $total, $customerId = null, $discount = 0, $notes = '')
    {
        $this->cart = $cart;
        $this->total = $total;
        $this->customerId = $customerId;
        $this->discount = $discount;
        $this->notes = $notes;
        $this->paymentAmount = $total;
    }

    public function addPayment()
    {
        if (!$this->selectedPaymentMethod || $this->paymentAmount <= 0) {
            session()->flash('error', 'Please select payment method and enter amount');
            return;
        }

        $this->payments[] = [
            'payment_method_id' => $this->selectedPaymentMethod,
            'amount' => $this->paymentAmount,
            'reference' => null,
        ];

        $remaining = $this->total - $this->getTotalPaid();
        $this->paymentAmount = $remaining > 0 ? $remaining : 0;
    }

    public function removePayment($index)
    {
        unset($this->payments[$index]);
        $this->payments = array_values($this->payments);
        
        $remaining = $this->total - $this->getTotalPaid();
        $this->paymentAmount = $remaining > 0 ? $remaining : 0;
    }

    public function completeSale()
    {
        if ($this->getTotalPaid() < $this->total) {
            session()->flash('error', 'Payment amount is less than total');
            return;
        }

        $saleService = app(SaleService::class);

        $sale = $saleService->createSale([
            'store_id' => Auth::user()->store_id,
            'user_id' => Auth::id(),
            'customer_id' => $this->customerId,
            'discount' => $this->discount,
            'notes' => $this->notes,
            'items' => collect($this->cart)->map(fn($item) => [
                'product_variant_id' => $item['variant_id'],
                'quantity' => $item['quantity'],
                'discount' => $item['discount'],
            ])->toArray(),
        ]);

        $saleService->completeSale($sale, $this->payments);

        $this->dispatch('paymentCompleted', $sale->id);
    }

    public function getTotalPaid()
    {
        return collect($this->payments)->sum('amount');
    }

    public function render()
    {
        $paymentMethods = PaymentMethod::active()->orderBy('sort_order')->get();
        return view('livewire.p-o-s.payment', compact('paymentMethods'));
    }
}