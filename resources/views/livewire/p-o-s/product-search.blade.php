<div>
    <input type="text"
           wire:model.live="search"
           placeholder="Search by name, SKU, or barcode..."
           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

    @if(!empty($products))
        <div class="mt-4 border rounded-lg divide-y max-h-96 overflow-y-auto">
            @foreach($products as $product)
                <div wire:click="selectProduct({{ $product['id'] }})"
                     class="p-4 hover:bg-gray-50 cursor-pointer transition-colors">
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">{{ $product['name'] }}</h3>
                            <div class="flex items-center space-x-4 mt-1">
                                <p class="text-sm text-gray-600">SKU: {{ $product['sku'] ?? 'N/A' }}</p>
                                <p class="text-sm {{ $product['is_low_stock'] ? 'text-orange-600 font-semibold' : 'text-gray-600' }}">
                                    Stock: {{ $product['stock_quantity'] }}
                                    @if($product['is_low_stock'])
                                        <span class="ml-1">⚠️</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <span class="font-bold text-lg text-blue-600">${{ number_format($product['price'], 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif(!empty($search) && empty($products))
        <div class="mt-4 text-center text-gray-500 py-8">
            <p>No products found matching "{{ $search }}"</p>
        </div>
    @endif
</div>
