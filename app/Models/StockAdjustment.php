<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['store_id', 'user_id', 'approved_by', 'reference', 'reason', 'status', 'notes', 'approved_at'];
    protected $casts = ['approved_at' => 'datetime'];
    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function items(): HasMany { return $this->hasMany(StockAdjustmentItem::class); }
    public function scopePending($query) { return $query->where('status', 'pending'); }
}
