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

    protected $fillable = ['category_id', 'brand_id', 'tax_rate_id', 'name', 'slug', 'sku', 'barcode', 'description', 'unit', 'active', 'featured', 'track_inventory'];

    protected $casts = ['active' => 'boolean', 'featured' => 'boolean', 'track_inventory' => 'boolean'];

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
    public function taxRate(): BelongsTo { return $this->belongsTo(TaxRate::class); }
    public function variants(): HasMany { return $this->hasMany(ProductVariant::class); }
    public function images(): HasMany { return $this->hasMany(ProductImage::class); }
    public function scopeActive($query) { return $query->where('active', true); }
    public function scopeSearch($query, string $search) { return $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")->orWhere('barcode', 'like', "%{$search}%")); }
}
