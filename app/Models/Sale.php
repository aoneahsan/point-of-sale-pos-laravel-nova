<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_COMPLETED = 'completed';
    const STATUS_PENDING = 'pending';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    protected $fillable = ['store_id', 'user_id', 'customer_id', 'reference', 'subtotal', 'tax', 'discount', 'total', 'status', 'notes'];

    protected $casts = ['subtotal' => 'decimal:2', 'tax' => 'decimal:2', 'discount' => 'decimal:2', 'total' => 'decimal:2'];

    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function items(): HasMany { return $this->hasMany(SaleItem::class); }
    public function payments(): HasMany { return $this->hasMany(SalePayment::class); }
    public function receipt(): HasOne { return $this->hasOne(Receipt::class); }
    public function returns(): HasMany { return $this->hasMany(SaleReturn::class); }
    public function scopeCompleted($query) { return $query->where('status', self::STATUS_COMPLETED); }
    public function scopeToday($query) { return $query->whereDate('created_at', today()); }
}
