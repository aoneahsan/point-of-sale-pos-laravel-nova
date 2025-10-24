<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Store Model
 *
 * Represents a physical store/branch location in the POS system.
 * Supports multi-tenant architecture where data is isolated per store.
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $tax_number
 * @property bool $active
 * @property array|null $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Store extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'tax_number',
        'active',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get the users assigned to this store.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the product variants for this store.
     */
    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the sales for this store.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get the purchase orders for this store.
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get the cash drawers for this store.
     */
    public function cashDrawers(): HasMany
    {
        return $this->hasMany(CashDrawer::class);
    }

    /**
     * Get the stock movements for this store.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get the stock adjustments for this store.
     */
    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    /**
     * Get the settings for this store.
     */
    public function storeSettings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    /**
     * Scope a query to only include active stores.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
