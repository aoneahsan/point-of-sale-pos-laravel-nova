<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Product Search & Cart -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Search -->
                <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                    <h2 class="text-lg sm:text-xl font-semibold mb-4">Product Search</h2>
                    <livewire:p-o-s.product-search />
                </div>

                <!-- Cart -->
                <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                    <h2 class="text-lg sm:text-xl font-semibold mb-4">Shopping Cart</h2>
                    <livewire:p-o-s.cart :cart="$cart" :subtotal="$this->subtotal" :discount="$discount" :total="$this->total" />
                </div>
            </div>

            <!-- Summary & Actions -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                    <h2 class="text-lg sm:text-xl font-semibold mb-4">Order Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal:</span>
                            <span class="font-semibold">${{ number_format($this->subtotal, 2) }}</span>
                        </div>
                        @if($discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount:</span>
                                <span class="font-semibold">-${{ number_format($discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg sm:text-xl font-bold border-t pt-3 text-blue-600">
                            <span>Total:</span>
                            <span>${{ number_format($this->total, 2) }}</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ count($cart) }} {{ count($cart) === 1 ? 'item' : 'items' }} in cart
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button wire:click="proceedToPayment"
                                @if(empty($cart)) disabled @endif
                                class="w-full bg-blue-600 text-white py-3 sm:py-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed font-semibold text-base sm:text-lg transition-colors">
                            Proceed to Payment
                        </button>
                        <button wire:click="holdSale"
                                @if(empty($cart)) disabled @endif
                                class="w-full bg-yellow-500 text-white py-2 sm:py-3 rounded-lg hover:bg-yellow-600 disabled:bg-gray-300 disabled:cursor-not-allowed font-medium transition-colors">
                            Hold Sale
                        </button>
                        <button wire:click="clearCart"
                                @if(empty($cart)) disabled @endif
                                class="w-full bg-gray-200 text-gray-700 py-2 sm:py-3 rounded-lg hover:bg-gray-300 disabled:bg-gray-100 disabled:cursor-not-allowed font-medium transition-colors">
                            Clear Cart
                        </button>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold mb-3">Quick Info</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Cashier:</span>
                            <span class="font-medium">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Store:</span>
                            <span class="font-medium">{{ auth()->user()->store->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Time:</span>
                            <span class="font-medium">{{ now()->format('h:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($showPayment)
        <!-- Payment Modal - Fully Responsive with Scrolling -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-2 sm:p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl flex flex-col max-h-[calc(100vh-1rem)] sm:max-h-[calc(100vh-2rem)]">
                <!-- Modal Header (fixed) -->
                <div class="flex-shrink-0 border-b px-4 sm:px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl sm:text-2xl font-bold">Payment</h2>
                    <button wire:click="$set('showPayment', false)"
                            class="text-gray-500 hover:text-gray-700 p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Content (scrollable) -->
                <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4">
                    <livewire:p-o-s.payment :cart="$cart" :total="$this->total" :discount="$discount" />
                </div>
            </div>
        </div>
    @endif
</div>
