<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['parent_id', 'name', 'slug', 'description', 'image', 'active', 'sort_order'];
    protected $casts = ['active' => 'boolean', 'sort_order' => 'integer'];
    public function parent(): BelongsTo { return $this->belongsTo(Category::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(Category::class, 'parent_id'); }
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function scopeActive($query) { return $query->where('active', true); }
    public function scopeRoot($query) { return $query->whereNull('parent_id'); }
}
