<div class="p-6">
    <h2 class="text-2xl font-bold mb-6">Payment</h2>
    
    <div class="mb-6">
        <h3 class="font-semibold mb-2">Total Amount: ${{ number_format($total, 2) }}</h3>
        <p class="text-sm text-gray-600">Remaining: ${{ number_format($total - $this->getTotalPaid(), 2) }}</p>
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium mb-2">Payment Method</label>
        <select wire:model="selectedPaymentMethod" class="w-full rounded border-gray-300">
            <option value="">Select payment method</option>
            @foreach($paymentMethods as $method)
                <option value="{{ $method->id }}">{{ $method->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium mb-2">Amount</label>
        <input type="number" wire:model="paymentAmount" step="0.01" class="w-full rounded border-gray-300">
    </div>

    <button wire:click="addPayment" class="w-full bg-green-600 text-white py-2 rounded mb-6 hover:bg-green-700">
        Add Payment
    </button>

    @if(!empty($payments))
        <div class="mb-6 border rounded p-4">
            <h3 class="font-semibold mb-2">Payments Added</h3>
            @foreach($payments as $index => $payment)
                <div class="flex justify-between items-center mb-2">
                    <span>{{ $paymentMethods->find($payment['payment_method_id'])->name }}</span>
                    <div>
                        <span class="mr-4">${{ number_format($payment['amount'], 2) }}</span>
                        <button wire:click="removePayment({{ $index }})" class="text-red-600">Remove</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="flex space-x-4">
        <button wire:click="completeSale" 
                @if($this->getTotalPaid() < $total) disabled @endif
                class="flex-1 bg-blue-600 text-white py-3 rounded hover:bg-blue-700 disabled:bg-gray-300">
            Complete Sale
        </button>
        <button wire:click="$dispatch('closePayment')" class="flex-1 bg-gray-200 py-3 rounded hover:bg-gray-300">
            Cancel
        </button>
    </div>
</div>
