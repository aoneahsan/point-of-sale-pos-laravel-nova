<div class="space-y-6">
    <!-- Payment Summary -->
    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-700 font-medium">Total Amount:</span>
            <span class="text-2xl font-bold text-blue-600">${{ number_format($total, 2) }}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-600 text-sm">Remaining:</span>
            <span class="text-lg font-semibold {{ ($total - $this->getTotalPaid()) > 0 ? 'text-orange-600' : 'text-green-600' }}">
                ${{ number_format($total - $this->getTotalPaid(), 2) }}
            </span>
        </div>
        @if($this->getTotalPaid() > 0)
            <div class="flex justify-between items-center mt-2 pt-2 border-t border-blue-300">
                <span class="text-gray-600 text-sm">Paid:</span>
                <span class="text-lg font-semibold text-green-600">${{ number_format($this->getTotalPaid(), 2) }}</span>
            </div>
        @endif
    </div>

    <!-- Payment Method Selection -->
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
            <select wire:model="selectedPaymentMethod"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select payment method</option>
                @foreach($paymentMethods as $method)
                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Amount *</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">$</span>
                <input type="number"
                       wire:model="paymentAmount"
                       step="0.01"
                       min="0.01"
                       placeholder="0.00"
                       class="w-full pl-8 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex space-x-2 mt-2">
                <button wire:click="$set('paymentAmount', {{ $total - $this->getTotalPaid() }})"
                        class="px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded transition-colors">
                    Exact Amount
                </button>
                <button wire:click="$set('paymentAmount', {{ $total }})"
                        class="px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded transition-colors">
                    Full Total
                </button>
            </div>
        </div>

        <button wire:click="addPayment"
                @if(empty($selectedPaymentMethod) || empty($paymentAmount) || $paymentAmount <= 0) disabled @endif
                class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed font-semibold transition-colors">
            + Add Payment
        </button>
    </div>

    <!-- Payments Added -->
    @if(!empty($payments))
        <div class="border rounded-lg p-4 bg-gray-50">
            <h3 class="font-semibold mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Payments Added ({{ count($payments) }})
            </h3>
            <div class="space-y-2">
                @foreach($payments as $index => $payment)
                    <div class="flex justify-between items-center bg-white p-3 rounded border">
                        <div class="flex-1">
                            <span class="font-medium text-gray-900">
                                {{ $paymentMethods->find($payment['payment_method_id'])->name }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="font-semibold text-green-600">${{ number_format($payment['amount'], 2) }}</span>
                            <button wire:click="removePayment({{ $index }})"
                                    class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 pt-4 border-t">
        <button wire:click="completeSale"
                @if($this->getTotalPaid() < $total) disabled @endif
                class="flex-1 bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed font-bold text-lg transition-colors shadow-md hover:shadow-lg">
            {{ $this->getTotalPaid() >= $total ? 'Complete Sale' : 'Payment Incomplete' }}
        </button>
        <button wire:click="$dispatch('closePayment')"
                class="sm:w-32 bg-gray-200 text-gray-700 py-4 rounded-lg hover:bg-gray-300 font-semibold transition-colors">
            Cancel
        </button>
    </div>

    <!-- Change Calculation (if overpaid) -->
    @if($this->getTotalPaid() > $total)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <span class="text-green-800 font-medium">Change Due:</span>
                <span class="text-2xl font-bold text-green-600">
                    ${{ number_format($this->getTotalPaid() - $total, 2) }}
                </span>
            </div>
        </div>
    @endif
</div>
