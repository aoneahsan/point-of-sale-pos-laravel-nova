<?php

namespace App\Livewire\POS;

use Livewire\Component;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Services\SaleService;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $cart = [];
    public $customerId = null;
    public $customer = null;
    public $searchTerm = '';
    public $discount = 0;
    public $notes = '';
    public $showPayment = false;

    protected $listeners = ['productAdded', 'customerSelected', 'paymentCompleted'];

    public function mount()
    {
        $this->cart = [];
    }

    public function addToCart($variantId)
    {
        $variant = ProductVariant::with('product')->findOrFail($variantId);

        if (!$variant->product->track_inventory || $variant->stock > 0) {
            $existingIndex = $this->findCartItemIndex($variantId);

            if ($existingIndex !== null) {
                $this->cart[$existingIndex]['quantity']++;
            } else {
                $this->cart[] = [
                    'variant_id' => $variant->id,
                    'name' => $variant->product->name . ' - ' . $variant->name,
                    'price' => $variant->price,
                    'quantity' => 1,
                    'discount' => 0,
                ];
            }
        }
    }

    public function updateQuantity($index, $quantity)
    {
        if ($quantity > 0) {
            $this->cart[$index]['quantity'] = $quantity;
        } else {
            $this->removeItem($index);
        }
    }

    public function removeItem($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->customerId = null;
        $this->customer = null;
        $this->discount = 0;
        $this->notes = '';
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity'] - $item['discount']);
    }

    public function getTotalProperty()
    {
        return $this->subtotal - $this->discount;
    }

    public function proceedToPayment()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Cart is empty');
            return;
        }

        $this->showPayment = true;
    }

    public function paymentCompleted($saleId)
    {
        $this->clearCart();
        $this->showPayment = false;
        session()->flash('success', 'Sale completed successfully!');
        $this->redirect(route('pos.receipt', $saleId));
    }

    protected function findCartItemIndex($variantId)
    {
        foreach ($this->cart as $index => $item) {
            if ($item['variant_id'] == $variantId) {
                return $index;
            }
        }
        return null;
    }

    public function render()
    {
        return view('livewire.p-o-s.index');
    }
}