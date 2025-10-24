<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['product_id', 'store_id', 'name', 'sku', 'barcode', 'price', 'cost', 'stock', 'low_stock_threshold', 'image', 'attributes'];

    protected $casts = ['price' => 'decimal:2', 'cost' => 'decimal:2', 'stock' => 'integer', 'low_stock_threshold' => 'integer', 'attributes' => 'array'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function stockMovements(): HasMany { return $this->hasMany(StockMovement::class); }
    public function saleItems(): HasMany { return $this->hasMany(SaleItem::class); }
    public function scopeLowStock($query) { return $query->whereColumn('stock', '<=', 'low_stock_threshold'); }
    public function scopeInStock($query) { return $query->where('stock', '>', 0); }
    public function isLowStock(): bool { return $this->stock <= $this->low_stock_threshold; }
}
