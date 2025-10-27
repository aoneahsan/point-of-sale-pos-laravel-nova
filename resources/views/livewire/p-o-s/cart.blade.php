<div class="space-y-4">
    @if(empty($cart))
        <div class="text-center text-gray-500 py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <p class="mt-4 text-lg">Your cart is empty</p>
            <p class="mt-1 text-sm">Search and add products to get started</p>
        </div>
    @else
        <!-- Cart Items -->
        <div class="space-y-3">
            @foreach($cart as $index => $item)
                <div class="flex items-center justify-between border rounded-lg p-4 hover:shadow-md transition-shadow bg-white">
                    <div class="flex-1 mr-4">
                        <h3 class="font-medium text-gray-900">{{ $item['name'] }}</h3>
                        <div class="flex items-center space-x-3 mt-1">
                            <p class="text-sm text-gray-600">SKU: {{ $item['sku'] ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600">${{ number_format($item['price'], 2) }} each</p>
                        </div>
                        @if(!empty($item['discount']) && $item['discount'] > 0)
                            <p class="text-sm text-green-600 mt-1">Discount: -${{ number_format($item['discount'], 2) }}</p>
                        @endif
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Quantity Controls -->
                        <div class="flex items-center space-x-2">
                            <button wire:click="decreaseQuantity({{ $index }})"
                                    class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-700 font-semibold">
                                −
                            </button>
                            <input type="number"
                                   wire:model.live="cart.{{ $index }}.quantity"
                                   wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                   class="w-16 text-center rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   min="1">
                            <button wire:click="increaseQuantity({{ $index }})"
                                    class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-700 font-semibold">
                                +
                            </button>
                        </div>

                        <!-- Item Total -->
                        <div class="w-24 text-right">
                            <p class="font-bold text-lg text-gray-900">
                                ${{ number_format(($item['price'] * $item['quantity']) - ($item['discount'] ?? 0), 2) }}
                            </p>
                        </div>

                        <!-- Remove Button -->
                        <button wire:click="removeItem({{ $index }})"
                                class="text-red-600 hover:text-red-800 p-2 rounded hover:bg-red-50 transition-colors"
                                title="Remove item">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Cart Summary -->
        <div class="border-t pt-4 mt-4">
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal ({{ count($cart) }} {{ count($cart) === 1 ? 'item' : 'items' }}):</span>
                    <span class="font-medium">${{ number_format($subtotal, 2) }}</span>
                </div>
                @if($discount > 0)
                    <div class="flex justify-between text-sm text-green-600">
                        <span>Total Discount:</span>
                        <span class="font-medium">-${{ number_format($discount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-lg font-bold border-t pt-2 mt-2">
                    <span>Total:</span>
                    <span class="text-blue-600">${{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Cart Actions -->
        <div class="flex space-x-3 pt-4">
            <button wire:click="clearCart"
                    wire:confirm="Are you sure you want to clear the cart?"
                    class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                Clear Cart
            </button>
            <button wire:click="holdSale"
                    class="flex-1 bg-yellow-500 text-white py-3 rounded-lg hover:bg-yellow-600 transition-colors font-medium">
                Hold Sale
            </button>
        </div>
    @endif
</div>
