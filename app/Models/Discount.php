<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'type', 'value', 'min_amount', 'max_uses', 'uses', 'start_date', 'end_date', 'conditions', 'active'];
    protected $casts = ['value' => 'decimal:2', 'min_amount' => 'decimal:2', 'max_uses' => 'integer', 'uses' => 'integer', 'start_date' => 'datetime', 'end_date' => 'datetime', 'conditions' => 'array', 'active' => 'boolean'];
    public function coupons(): HasMany { return $this->hasMany(Coupon::class); }
    public function scopeActive($query) { return $query->where('active', true)->where(function($q) { $q->whereNull('start_date')->orWhere('start_date', '<=', now()); })->where(function($q) { $q->whereNull('end_date')->orWhere('end_date', '>=', now()); }); }
}
