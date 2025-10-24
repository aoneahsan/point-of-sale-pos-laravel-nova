<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;
    protected $fillable = ['product_variant_id', 'store_id', 'user_id', 'type', 'quantity', 'reference', 'notes'];
    protected $casts = ['quantity' => 'integer'];
    public function productVariant(): BelongsTo { return $this->belongsTo(ProductVariant::class); }
    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
