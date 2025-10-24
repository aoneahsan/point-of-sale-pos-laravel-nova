<div>
    <input type="text" 
           wire:model.live="search" 
           placeholder="Search by name, SKU, or barcode..." 
           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    
    @if(!empty($results))
        <div class="mt-4 border rounded-lg divide-y max-h-96 overflow-y-auto">
            @foreach($results as $result)
                <div wire:click="selectProduct({{ $result->id }})" 
                     class="p-4 hover:bg-gray-50 cursor-pointer">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-medium">{{ $result->product->name }} - {{ $result->name }}</h3>
                            <p class="text-sm text-gray-600">SKU: {{ $result->sku }} | Stock: {{ $result->stock }}</p>
                        </div>
                        <span class="font-bold text-lg">${{ number_format($result->price, 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
