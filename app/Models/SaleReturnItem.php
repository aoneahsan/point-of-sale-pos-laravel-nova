<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleReturnItem extends Model
{
    use HasFactory;
    protected $table = 'return_items';
    protected $fillable = ['return_id', 'sale_item_id', 'quantity', 'unit_price', 'total'];
    protected $casts = ['quantity' => 'integer', 'unit_price' => 'decimal:2', 'total' => 'decimal:2'];
    public function return(): BelongsTo { return $this->belongsTo(SaleReturn::class, 'return_id'); }
    public function saleItem(): BelongsTo { return $this->belongsTo(SaleItem::class); }
}
