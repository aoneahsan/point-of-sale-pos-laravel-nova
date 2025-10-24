<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'store_id' => $this->store_id,
            'user_id' => $this->user_id,
            'customer_id' => $this->customer_id,
            'reference' => $this->reference,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'total' => $this->total,
            'status' => $this->status,
            'notes' => $this->notes,
            'items' => SaleItemResource::collection($this->whenLoaded('items')),
            'customer' => $this->whenLoaded('customer'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}