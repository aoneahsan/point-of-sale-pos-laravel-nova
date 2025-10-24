<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['supplier_id', 'store_id', 'user_id', 'reference', 'order_date', 'expected_date', 'status', 'subtotal', 'tax', 'total', 'notes'];
    protected $casts = ['order_date' => 'date', 'expected_date' => 'date', 'subtotal' => 'decimal:2', 'tax' => 'decimal:2', 'total' => 'decimal:2'];
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function items(): HasMany { return $this->hasMany(PurchaseOrderItem::class); }
    public function scopePending($query) { return $query->where('status', 'pending'); }
}
