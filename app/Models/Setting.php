<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = ['store_id', 'key', 'value'];
    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public static function get(string $key, ?$default = null, ?int $storeId = null) { return static::where('key', $key)->when($storeId, fn($q) => $q->where('store_id', $storeId))->value('value') ?? $default; }
}
