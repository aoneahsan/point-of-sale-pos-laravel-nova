<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashDrawer extends Model
{
    use HasFactory;
    protected $fillable = ['store_id', 'user_id', 'closed_by', 'opened_at', 'closed_at', 'opening_cash', 'expected_cash', 'actual_cash', 'difference', 'status', 'notes'];
    protected $casts = ['opened_at' => 'datetime', 'closed_at' => 'datetime', 'opening_cash' => 'decimal:2', 'expected_cash' => 'decimal:2', 'actual_cash' => 'decimal:2', 'difference' => 'decimal:2'];
    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function closedBy(): BelongsTo { return $this->belongsTo(User::class, 'closed_by'); }
    public function transactions(): HasMany { return $this->hasMany(CashTransaction::class); }
    public function scopeOpen($query) { return $query->where('status', 'open'); }
}
