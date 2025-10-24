<div class="max-w-2xl mx-auto p-8 bg-white">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold">{{ $sale->store->name }}</h1>
        <p class="text-sm">{{ $sale->store->address }}</p>
        <p class="text-sm">{{ $sale->store->phone }}</p>
    </div>

    <div class="border-t border-b py-4 mb-4">
        <p><strong>Receipt #:</strong> {{ $sale->reference }}</p>
        <p><strong>Date:</strong> {{ $sale->created_at->format('Y-m-d H:i') }}</p>
        @if($sale->customer)
            <p><strong>Customer:</strong> {{ $sale->customer->name }}</p>
        @endif
    </div>

    <table class="w-full mb-4">
        <thead class="border-b">
            <tr>
                <th class="text-left py-2">Item</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr class="border-b">
                    <td class="py-2">{{ $item->product_name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-t pt-4 space-y-2">
        <div class="flex justify-between">
            <span>Subtotal:</span>
            <span>${{ number_format($sale->subtotal, 2) }}</span>
        </div>
        <div class="flex justify-between">
            <span>Tax:</span>
            <span>${{ number_format($sale->tax, 2) }}</span>
        </div>
        @if($sale->discount > 0)
            <div class="flex justify-between">
                <span>Discount:</span>
                <span>-${{ number_format($sale->discount, 2) }}</span>
            </div>
        @endif
        <div class="flex justify-between text-xl font-bold border-t pt-2">
            <span>Total:</span>
            <span>${{ number_format($sale->total, 2) }}</span>
        </div>
    </div>

    <div class="mt-6 border-t pt-4">
        <h3 class="font-semibold mb-2">Payments</h3>
        @foreach($sale->payments as $payment)
            <div class="flex justify-between">
                <span>{{ $payment->paymentMethod->name }}</span>
                <span>${{ number_format($payment->amount, 2) }}</span>
            </div>
        @endforeach
    </div>

    <div class="mt-8 text-center text-sm">
        <p>Thank you for your business!</p>
    </div>

    <div class="mt-6 flex space-x-4">
        <button wire:click="print" class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Print Receipt
        </button>
        <button wire:click="newSale" class="flex-1 bg-gray-200 py-2 rounded hover:bg-gray-300">
            New Sale
        </button>
    </div>
</div>
