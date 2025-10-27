<?php

namespace App\Livewire\POS;

use Livewire\Component;
use App\Models\Product;
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

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        // Check if product is in stock (if tracking is enabled)
        if (!$product->track_stock || $product->stock_quantity > 0) {
            $existingIndex = $this->findCartItemIndex($productId);

            if ($existingIndex !== null) {
                // Increment quantity if item already in cart
                $newQuantity = $this->cart[$existingIndex]['quantity'] + 1;

                // Verify stock availability
                if ($product->track_stock && $newQuantity > $product->stock_quantity) {
                    session()->flash('error', 'Insufficient stock for ' . $product->name);
                    return;
                }

                $this->cart[$existingIndex]['quantity'] = $newQuantity;
            } else {
                $this->cart[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'cost' => $product->cost,
                    'quantity' => 1,
                    'discount' => 0,
                ];
            }
        } else {
            session()->flash('error', 'Product out of stock: ' . $product->name);
        }
    }

    public function updateQuantity($index, $quantity)
    {
        if ($quantity > 0) {
            // Get the product to verify stock availability
            $product = Product::find($this->cart[$index]['product_id']);

            if ($product && $product->track_stock && $quantity > $product->stock_quantity) {
                session()->flash('error', 'Insufficient stock. Available: ' . $product->stock_quantity);
                return;
            }

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

    protected function findCartItemIndex($productId)
    {
        foreach ($this->cart as $index => $item) {
            if ($item['product_id'] == $productId) {
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