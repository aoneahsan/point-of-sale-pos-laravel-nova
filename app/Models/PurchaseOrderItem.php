<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;
    protected $fillable = ['purchase_order_id', 'product_variant_id', 'quantity', 'unit_cost', 'total', 'received_quantity'];
    protected $casts = ['quantity' => 'integer', 'unit_cost' => 'decimal:2', 'total' => 'decimal:2', 'received_quantity' => 'integer'];
    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class); }
    public function productVariant(): BelongsTo { return $this->belongsTo(ProductVariant::class); }
}
