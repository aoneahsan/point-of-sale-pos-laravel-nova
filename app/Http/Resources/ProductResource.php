<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'store_id' => $this->store_id,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'tax_rate_id' => $this->tax_rate_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'unit' => $this->unit,
            'price' => $this->price,
            'cost' => $this->cost,
            'stock_quantity' => $this->stock_quantity,
            'reorder_point' => $this->reorder_point,
            'track_stock' => $this->track_stock,
            'active' => $this->active,
            'featured' => $this->featured,
            'track_inventory' => $this->track_inventory,
            'is_low_stock' => $this->isLowStock(),
            'category' => $this->whenLoaded('category'),
            'brand' => $this->whenLoaded('brand'),
            'variants' => $this->whenLoaded('variants'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}