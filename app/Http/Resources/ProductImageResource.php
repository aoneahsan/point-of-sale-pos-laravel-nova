<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Product Image API Resource.
 *
 * @property \App\Models\ProductImage $resource
 */
class ProductImageResource extends JsonResource
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
            'url' => $this->resource->url,
            'alt_text' => $this->resource->alt_text,
            'sort_order' => $this->resource->sort_order,
            'is_primary' => $this->resource->is_primary,
            'created_at' => $this->resource->created_at?->toISOString(),
        ];
    }
}
