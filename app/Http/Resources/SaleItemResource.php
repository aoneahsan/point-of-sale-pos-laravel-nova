<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sale_id' => $this->sale_id,
            'product_variant_id' => $this->product_variant_id,
            'product_name' => $this->product_name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'subtotal' => $this->subtotal,
            'variant' => $this->whenLoaded('variant'),
        ];
    }
}