<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturn extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'returns';
    protected $fillable = ['sale_id', 'store_id', 'user_id', 'approved_by', 'reference', 'reason', 'subtotal', 'tax', 'total', 'status', 'notes', 'approved_at'];
    protected $casts = ['subtotal' => 'decimal:2', 'tax' => 'decimal:2', 'total' => 'decimal:2', 'approved_at' => 'datetime'];
    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function items(): HasMany { return $this->hasMany(SaleReturnItem::class, 'return_id'); }
    public function scopePending($query) { return $query->where('status', 'pending'); }
}
