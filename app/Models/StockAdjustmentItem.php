<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustmentItem extends Model
{
    use HasFactory;
    protected $fillable = ['stock_adjustment_id', 'product_variant_id', 'quantity_before', 'quantity_after', 'difference'];
    protected $casts = ['quantity_before' => 'integer', 'quantity_after' => 'integer', 'difference' => 'integer'];
    public function stockAdjustment(): BelongsTo { return $this->belongsTo(StockAdjustment::class); }
    public function productVariant(): BelongsTo { return $this->belongsTo(ProductVariant::class); }
}
