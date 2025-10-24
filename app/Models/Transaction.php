<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['transactionable_type', 'transactionable_id', 'type', 'amount', 'reference', 'notes'];
    protected $casts = ['amount' => 'decimal:2'];
    public function transactionable(): MorphTo { return $this->morphTo(); }
}
