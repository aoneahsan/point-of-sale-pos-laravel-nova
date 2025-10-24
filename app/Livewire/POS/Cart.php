<?php

namespace App\Livewire\POS;

use Livewire\Component;

class Cart extends Component
{
    public $cart = [];
    public $discount = 0;

    protected $listeners = ['cartUpdated'];

    public function updateQuantity($index, $quantity)
    {
        $this->dispatch('updateQuantity', $index, $quantity);
    }

    public function removeItem($index)
    {
        $this->dispatch('removeItem', $index);
    }

    public function applyDiscount($amount)
    {
        $this->discount = $amount;
        $this->dispatch('discountApplied', $amount);
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity'] - $item['discount']);
    }

    public function getTotalProperty()
    {
        return $this->subtotal - $this->discount;
    }

    public function render()
    {
        return view('livewire.p-o-s.cart');
    }
}