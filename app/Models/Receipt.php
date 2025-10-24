<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory;
    protected $fillable = ['sale_id', 'template', 'printed_at', 'emailed_at'];
    protected $casts = ['printed_at' => 'datetime', 'emailed_at' => 'datetime'];
    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
}
