#!/bin/bash

# This script implements all remaining model files with their relationships
# Run with: bash implement_all_models.sh

echo "Implementing all remaining models..."

# Array of models to implement (model_file:model_content)
declare -A models

models["Brand"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;
    protected \$fillable = ['name', 'slug', 'logo', 'description', 'active'];
    protected \$casts = ['active' => 'boolean'];
    public function products(): HasMany { return \$this->hasMany(Product::class); }
    public function scopeActive(\$query) { return \$query->where('active', true); }
}"

models["Category"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected \$fillable = ['parent_id', 'name', 'slug', 'description', 'image', 'active', 'sort_order'];
    protected \$casts = ['active' => 'boolean', 'sort_order' => 'integer'];
    public function parent(): BelongsTo { return \$this->belongsTo(Category::class, 'parent_id'); }
    public function children(): HasMany { return \$this->hasMany(Category::class, 'parent_id'); }
    public function products(): HasMany { return \$this->hasMany(Product::class); }
    public function scopeActive(\$query) { return \$query->where('active', true); }
    public function scopeRoot(\$query) { return \$query->whereNull('parent_id'); }
}"

models["ProductImage"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;
    protected \$fillable = ['product_id', 'image_path', 'is_primary', 'sort_order'];
    protected \$casts = ['is_primary' => 'boolean', 'sort_order' => 'integer'];
    public function product(): BelongsTo { return \$this->belongsTo(Product::class); }
    public function scopePrimary(\$query) { return \$query->where('is_primary', true); }
}"

models["SaleItem"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;
    protected \$fillable = ['sale_id', 'product_variant_id', 'quantity', 'unit_price', 'discount', 'tax', 'total'];
    protected \$casts = ['quantity' => 'integer', 'unit_price' => 'decimal:2', 'discount' => 'decimal:2', 'tax' => 'decimal:2', 'total' => 'decimal:2'];
    public function sale(): BelongsTo { return \$this->belongsTo(Sale::class); }
    public function productVariant(): BelongsTo { return \$this->belongsTo(ProductVariant::class); }
}"

models["SalePayment"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePayment extends Model
{
    use HasFactory;
    protected \$fillable = ['sale_id', 'payment_method_id', 'amount', 'reference'];
    protected \$casts = ['amount' => 'decimal:2'];
    public function sale(): BelongsTo { return \$this->belongsTo(Sale::class); }
    public function paymentMethod(): BelongsTo { return \$this->belongsTo(PaymentMethod::class); }
}"

models["CustomerGroup"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerGroup extends Model
{
    use HasFactory, SoftDeletes;
    protected \$fillable = ['name', 'discount_percentage', 'description', 'active'];
    protected \$casts = ['discount_percentage' => 'decimal:2', 'active' => 'boolean'];
    public function customers(): HasMany { return \$this->hasMany(Customer::class); }
    public function scopeActive(\$query) { return \$query->where('active', true); }
}"

models["PaymentMethod"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;
    protected \$fillable = ['name', 'type', 'active', 'settings'];
    protected \$casts = ['active' => 'boolean', 'settings' => 'array'];
    public function salePayments(): HasMany { return \$this->hasMany(SalePayment::class); }
    public function scopeActive(\$query) { return \$query->where('active', true); }
}"

models["TaxRate"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRate extends Model
{
    use HasFactory, SoftDeletes;
    protected \$fillable = ['name', 'rate', 'description', 'active'];
    protected \$casts = ['rate' => 'decimal:2', 'active' => 'boolean'];
    public function scopeActive(\$query) { return \$query->where('active', true); }
}"

models["SaleReturn"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturn extends Model
{
    use HasFactory, SoftDeletes;
    protected \$table = 'returns';
    protected \$fillable = ['sale_id', 'store_id', 'user_id', 'approved_by', 'reference', 'reason', 'subtotal', 'tax', 'total', 'status', 'notes', 'approved_at'];
    protected \$casts = ['subtotal' => 'decimal:2', 'tax' => 'decimal:2', 'total' => 'decimal:2', 'approved_at' => 'datetime'];
    public function sale(): BelongsTo { return \$this->belongsTo(Sale::class); }
    public function store(): BelongsTo { return \$this->belongsTo(Store::class); }
    public function user(): BelongsTo { return \$this->belongsTo(User::class); }
    public function approver(): BelongsTo { return \$this->belongsTo(User::class, 'approved_by'); }
    public function items(): HasMany { return \$this->hasMany(SaleReturnItem::class, 'return_id'); }
    public function scopePending(\$query) { return \$query->where('status', 'pending'); }
}"

models["SaleReturnItem"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleReturnItem extends Model
{
    use HasFactory;
    protected \$table = 'return_items';
    protected \$fillable = ['return_id', 'sale_item_id', 'quantity', 'unit_price', 'total'];
    protected \$casts = ['quantity' => 'integer', 'unit_price' => 'decimal:2', 'total' => 'decimal:2'];
    public function return(): BelongsTo { return \$this->belongsTo(SaleReturn::class, 'return_id'); }
    public function saleItem(): BelongsTo { return \$this->belongsTo(SaleItem::class); }
}"

models["Supplier"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;
    protected \$fillable = ['name', 'email', 'phone', 'address', 'tax_number', 'notes', 'active'];
    protected \$casts = ['active' => 'boolean'];
    public function purchaseOrders(): HasMany { return \$this->hasMany(PurchaseOrder::class); }
    public function scopeActive(\$query) { return \$query->where('active', true); }
}"

models["PurchaseOrder"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;
    protected \$fillable = ['supplier_id', 'store_id', 'user_id', 'reference', 'order_date', 'expected_date', 'status', 'subtotal', 'tax', 'total', 'notes'];
    protected \$casts = ['order_date' => 'date', 'expected_date' => 'date', 'subtotal' => 'decimal:2', 'tax' => 'decimal:2', 'total' => 'decimal:2'];
    public function supplier(): BelongsTo { return \$this->belongsTo(Supplier::class); }
    public function store(): BelongsTo { return \$this->belongsTo(Store::class); }
    public function user(): BelongsTo { return \$this->belongsTo(User::class); }
    public function items(): HasMany { return \$this->hasMany(PurchaseOrderItem::class); }
    public function scopePending(\$query) { return \$query->where('status', 'pending'); }
}"

models["PurchaseOrderItem"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;
    protected \$fillable = ['purchase_order_id', 'product_variant_id', 'quantity', 'unit_cost', 'total', 'received_quantity'];
    protected \$casts = ['quantity' => 'integer', 'unit_cost' => 'decimal:2', 'total' => 'decimal:2', 'received_quantity' => 'integer'];
    public function purchaseOrder(): BelongsTo { return \$this->belongsTo(PurchaseOrder::class); }
    public function productVariant(): BelongsTo { return \$this->belongsTo(ProductVariant::class); }
}"

models["StockMovement"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;
    protected \$fillable = ['product_variant_id', 'store_id', 'user_id', 'type', 'quantity', 'reference', 'notes'];
    protected \$casts = ['quantity' => 'integer'];
    public function productVariant(): BelongsTo { return \$this->belongsTo(ProductVariant::class); }
    public function store(): BelongsTo { return \$this->belongsTo(Store::class); }
    public function user(): BelongsTo { return \$this->belongsTo(User::class); }
}"

models["StockAdjustment"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustment extends Model
{
    use HasFactory, SoftDeletes;
    protected \$fillable = ['store_id', 'user_id', 'approved_by', 'reference', 'reason', 'status', 'notes', 'approved_at'];
    protected \$casts = ['approved_at' => 'datetime'];
    public function store(): BelongsTo { return \$this->belongsTo(Store::class); }
    public function user(): BelongsTo { return \$this->belongsTo(User::class); }
    public function approver(): BelongsTo { return \$this->belongsTo(User::class, 'approved_by'); }
    public function items(): HasMany { return \$this->hasMany(StockAdjustmentItem::class); }
    public function scopePending(\$query) { return \$query->where('status', 'pending'); }
}"

models["StockAdjustmentItem"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustmentItem extends Model
{
    use HasFactory;
    protected \$fillable = ['stock_adjustment_id', 'product_variant_id', 'quantity_before', 'quantity_after', 'difference'];
    protected \$casts = ['quantity_before' => 'integer', 'quantity_after' => 'integer', 'difference' => 'integer'];
    public function stockAdjustment(): BelongsTo { return \$this->belongsTo(StockAdjustment::class); }
    public function productVariant(): BelongsTo { return \$this->belongsTo(ProductVariant::class); }
}"

models["Discount"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory, SoftDeletes;
    protected \$fillable = ['name', 'type', 'value', 'min_amount', 'max_uses', 'uses', 'start_date', 'end_date', 'conditions', 'active'];
    protected \$casts = ['value' => 'decimal:2', 'min_amount' => 'decimal:2', 'max_uses' => 'integer', 'uses' => 'integer', 'start_date' => 'datetime', 'end_date' => 'datetime', 'conditions' => 'array', 'active' => 'boolean'];
    public function coupons(): HasMany { return \$this->hasMany(Coupon::class); }
    public function scopeActive(\$query) { return \$query->where('active', true)->where(function(\$q) { \$q->whereNull('start_date')->orWhere('start_date', '<=', now()); })->where(function(\$q) { \$q->whereNull('end_date')->orWhere('end_date', '>=', now()); }); }
}"

models["Coupon"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;
    protected \$fillable = ['discount_id', 'code', 'max_uses', 'uses', 'max_uses_per_customer', 'expires_at', 'active'];
    protected \$casts = ['max_uses' => 'integer', 'uses' => 'integer', 'max_uses_per_customer' => 'integer', 'expires_at' => 'datetime', 'active' => 'boolean'];
    public function discount(): BelongsTo { return \$this->belongsTo(Discount::class); }
    public function scopeActive(\$query) { return \$query->where('active', true)->where(function(\$q) { \$q->whereNull('expires_at')->orWhere('expires_at', '>=', now()); }); }
}"

models["CashDrawer"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashDrawer extends Model
{
    use HasFactory;
    protected \$fillable = ['store_id', 'user_id', 'closed_by', 'opened_at', 'closed_at', 'opening_cash', 'expected_cash', 'actual_cash', 'difference', 'status', 'notes'];
    protected \$casts = ['opened_at' => 'datetime', 'closed_at' => 'datetime', 'opening_cash' => 'decimal:2', 'expected_cash' => 'decimal:2', 'actual_cash' => 'decimal:2', 'difference' => 'decimal:2'];
    public function store(): BelongsTo { return \$this->belongsTo(Store::class); }
    public function user(): BelongsTo { return \$this->belongsTo(User::class); }
    public function closedBy(): BelongsTo { return \$this->belongsTo(User::class, 'closed_by'); }
    public function transactions(): HasMany { return \$this->hasMany(CashTransaction::class); }
    public function scopeOpen(\$query) { return \$query->where('status', 'open'); }
}"

models["CashTransaction"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransaction extends Model
{
    use HasFactory;
    protected \$fillable = ['cash_drawer_id', 'type', 'amount', 'reference', 'reason', 'notes'];
    protected \$casts = ['amount' => 'decimal:2'];
    public function cashDrawer(): BelongsTo { return \$this->belongsTo(CashDrawer::class); }
}"

models["Setting"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;
    protected \$fillable = ['store_id', 'key', 'value'];
    public function store(): BelongsTo { return \$this->belongsTo(Store::class); }
    public static function get(string \$key, ?\$default = null, ?int \$storeId = null) { return static::where('key', \$key)->when(\$storeId, fn(\$q) => \$q->where('store_id', \$storeId))->value('value') ?? \$default; }
}"

models["Receipt"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory;
    protected \$fillable = ['sale_id', 'template', 'printed_at', 'emailed_at'];
    protected \$casts = ['printed_at' => 'datetime', 'emailed_at' => 'datetime'];
    public function sale(): BelongsTo { return \$this->belongsTo(Sale::class); }
}"

models["Transaction"]="<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasFactory;
    protected \$fillable = ['transactionable_type', 'transactionable_id', 'type', 'amount', 'reference', 'notes'];
    protected \$casts = ['amount' => 'decimal:2'];
    public function transactionable(): MorphTo { return \$this->morphTo(); }
}"

# Implement all models
for model in "${!models[@]}"; do
    echo "Implementing $model model..."
    echo "${models[$model]}" > "app/Models/$model.php"
done

echo ""
echo "✅ All models implemented successfully!"
echo "Total models: ${#models[@]}"
