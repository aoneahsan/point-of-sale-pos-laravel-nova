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
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'unit' => $this->unit,
            'active' => $this->active,
            'featured' => $this->featured,
            'track_inventory' => $this->track_inventory,
            'category' => $this->whenLoaded('category'),
            'brand' => $this->whenLoaded('brand'),
            'variants' => $this->whenLoaded('variants'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}