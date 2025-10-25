<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Customer Model
 *
 * Represents a customer in the POS system with loyalty points,
 * store credit, and purchase history tracking.
 *
 * @property int $id
 * @property int|null $customer_group_id
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property int $loyalty_points
 * @property float $store_credit
 * @property string|null $date_of_birth
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id',
        'customer_group_id',
        'code',
        'name',
        'email',
        'phone',
        'address',
        'tax_number',
        'loyalty_points',
        'store_credit',
        'date_of_birth',
        'notes',
        'active',
    ];

    protected $casts = [
        'loyalty_points' => 'integer',
        'store_credit' => 'float',
        'date_of_birth' => 'date',
        'active' => 'boolean',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    public function addLoyaltyPoints(int $points): void
    {
        $this->increment('loyalty_points', $points);
    }

    public function deductLoyaltyPoints(int $points): void
    {
        $this->decrement('loyalty_points', $points);
    }

    public function addStoreCredit(float $amount): void
    {
        $this->increment('store_credit', $amount);
    }

    public function deductStoreCredit(float $amount): void
    {
        $this->decrement('store_credit', $amount);
    }

    public function getTotalPurchasesAttribute(): float
    {
        return $this->sales()->completed()->sum('total');
    }
}
