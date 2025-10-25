<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Sale Payment API Resource.
 *
 * @property \App\Models\SalePayment $resource
 */
class SalePaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'sale_id' => $this->resource->sale_id,
            'payment_method_id' => $this->resource->payment_method_id,
            'amount' => (float) $this->resource->amount,
            'reference_number' => $this->resource->reference_number,
            'status' => $this->resource->status,
            'created_at' => $this->resource->created_at?->toISOString(),
        ];
    }
}
