<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Product Search & Cart -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Search -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Product Search</h2>
                    <livewire:p-o-s.product-search />
                </div>

                <!-- Cart -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Shopping Cart</h2>
                    @if(empty($cart))
                        <p class="text-gray-500 text-center py-8">Cart is empty</p>
                    @else
                        <div class="space-y-4">
                            @foreach($cart as $index => $item)
                                <div class="flex items-center justify-between border-b pb-4">
                                    <div class="flex-1">
                                        <h3 class="font-medium">{{ $item['name'] }}</h3>
                                        <p class="text-sm text-gray-600">${{ number_format($item['price'], 2) }}</p>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <input type="number" wire:model="cart.{{ $index }}.quantity" 
                                               wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                               class="w-20 rounded border-gray-300" min="1">
                                        <span class="font-semibold">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                        <button wire:click="removeItem({{ $index }})" class="text-red-600 hover:text-red-800">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Summary & Actions -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Summary</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span class="font-semibold">${{ number_format($this->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Discount:</span>
                            <span class="font-semibold">${{ number_format($discount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                            <span>Total:</span>
                            <span>${{ number_format($this->total, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button wire:click="proceedToPayment" 
                                @if(empty($cart)) disabled @endif
                                class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 disabled:bg-gray-300">
                            Proceed to Payment
                        </button>
                        <button wire:click="clearCart" class="w-full bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300">
                            Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($showPayment)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <livewire:p-o-s.payment :cart="$cart" :total="$this->total" :discount="$discount" />
            </div>
        </div>
    @endif
</div>
