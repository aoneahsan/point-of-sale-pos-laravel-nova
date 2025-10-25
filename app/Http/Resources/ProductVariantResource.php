<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Product Variant API Resource.
 *
 * @property \App\Models\ProductVariant $resource
 */
class ProductVariantResource extends JsonResource
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
            'product_id' => $this->resource->product_id,
            'name' => $this->resource->name,
            'sku' => $this->resource->sku,
            'barcode' => $this->resource->barcode,
            'price' => (float) $this->resource->price,
            'cost' => (float) $this->resource->cost,
            'stock_quantity' => $this->resource->stock_quantity,
            'is_active' => $this->resource->is_active,
            'created_at' => $this->resource->created_at?->toISOString(),
            'updated_at' => $this->resource->updated_at?->toISOString(),
        ];
    }
}
