<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerGroup extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'code', 'discount_percentage', 'loyalty_points_multiplier', 'description', 'active'];
    protected $casts = ['discount_percentage' => 'decimal:2', 'loyalty_points_multiplier' => 'decimal:1', 'active' => 'boolean'];
    public function customers(): HasMany { return $this->hasMany(Customer::class); }
    public function scopeActive($query) { return $query->where('active', true); }
}
