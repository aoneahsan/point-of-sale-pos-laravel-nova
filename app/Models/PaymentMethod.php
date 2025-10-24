<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'code', 'type', 'active', 'sort_order', 'settings'];
    protected $casts = ['active' => 'boolean', 'sort_order' => 'integer', 'settings' => 'array'];
    public function salePayments(): HasMany { return $this->hasMany(SalePayment::class); }
    public function scopeActive($query) { return $query->where('active', true); }
}
