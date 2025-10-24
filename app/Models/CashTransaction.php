<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransaction extends Model
{
    use HasFactory;
    protected $fillable = ['cash_drawer_id', 'type', 'amount', 'reference', 'reason', 'notes'];
    protected $casts = ['amount' => 'decimal:2'];
    public function cashDrawer(): BelongsTo { return $this->belongsTo(CashDrawer::class); }
}
