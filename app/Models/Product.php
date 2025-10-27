<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id',
        'category_id',
        'brand_id',
        'tax_rate_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'description',
        'unit',
        'price',
        'cost',
        'stock_quantity',
        'reorder_point',
        'track_stock',
        'active',
        'featured',
        'track_inventory',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock_quantity' => 'integer',
        'reorder_point' => 'integer',
        'track_stock' => 'boolean',
        'active' => 'boolean',
        'featured' => 'boolean',
        'track_inventory' => 'boolean',
    ];

    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
    public function taxRate(): BelongsTo { return $this->belongsTo(TaxRate::class); }
    public function variants(): HasMany { return $this->hasMany(ProductVariant::class); }
    public function images(): HasMany { return $this->hasMany(ProductImage::class); }
    public function scopeActive($query) { return $query->where('active', true); }
    public function scopeLowStock($query) { return $query->where('track_stock', true)->whereColumn('stock_quantity', '<=', 'reorder_point'); }
    public function scopeInStock($query) { return $query->where('stock_quantity', '>', 0); }
    public function scopeForStore($query, int $storeId) { return $query->where('store_id', $storeId); }
    public function scopeSearch($query, string $search) { return $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")->orWhere('barcode', 'like', "%{$search}%")); }
    public function isLowStock(): bool { return $this->track_stock && $this->stock_quantity <= $this->reorder_point; }
}
