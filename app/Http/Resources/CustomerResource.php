<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'store_id' => $this->store_id,
            'customer_group_id' => $this->customer_group_id,
            'code' => $this->code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'loyalty_points' => $this->loyalty_points,
            'store_credit' => $this->store_credit,
            'active' => $this->active,
            'group' => $this->whenLoaded('group'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}