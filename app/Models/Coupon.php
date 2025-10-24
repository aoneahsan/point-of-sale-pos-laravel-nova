<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['discount_id', 'code', 'max_uses', 'uses', 'max_uses_per_customer', 'expires_at', 'active'];
    protected $casts = ['max_uses' => 'integer', 'uses' => 'integer', 'max_uses_per_customer' => 'integer', 'expires_at' => 'datetime', 'active' => 'boolean'];
    public function discount(): BelongsTo { return $this->belongsTo(Discount::class); }
    public function scopeActive($query) { return $query->where('active', true)->where(function($q) { $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()); }); }
}
