# POS System - Technical Architecture

## System Overview

The POS System is built using a modern, scalable architecture following Laravel best practices and clean code principles.

## Architecture Layers

```
┌─────────────────────────────────────────────────────────────┐
│                    Presentation Layer                        │
│  ┌──────────────────┐  ┌──────────────────┐  ┌────────────┐│
│  │   Nova Admin     │  │  Livewire POS    │  │  REST API  ││
│  │ (Inertia + Vue3) │  │   (Alpine.js)    │  │  (JSON)    ││
│  └──────────────────┘  └──────────────────┘  └────────────┘│
└─────────────────────────────────────────────────────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                   Application Layer                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐ │
│  │ Controllers  │  │   Requests   │  │    Resources     │ │
│  └──────────────┘  └──────────────┘  └──────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    Domain Layer                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐ │
│  │   Services   │  │   Observers  │  │     Events       │ │
│  │  (Business   │  │  (Hooks)     │  │   (Listeners)    │ │
│  │    Logic)    │  └──────────────┘  └──────────────────┘ │
│  └──────────────┘                                           │
└─────────────────────────────────────────────────────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    Data Layer                                │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐ │
│  │   Models     │  │ Repositories │  │    Database      │ │
│  │ (Eloquent)   │  │  (Complex    │  │   (MySQL)        │ │
│  │              │  │   Queries)   │  │                  │ │
│  └──────────────┘  └──────────────┘  └──────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                Infrastructure Layer                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐ │
│  │    Redis     │  │    Queue     │  │     Storage      │ │
│  │ (Cache/      │  │   Workers    │  │   (Files/S3)     │ │
│  │  Session)    │  │              │  │                  │ │
│  └──────────────┘  └──────────────┘  └──────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

## Design Patterns

### 1. Service Layer Pattern

**Purpose**: Separate business logic from controllers.

**Implementation:**
```php
// app/Services/SaleService.php
class SaleService
{
    public function __construct(
        private InventoryService $inventoryService,
        private TaxService $taxService,
        private DiscountService $discountService,
    ) {}

    public function processSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            // Create sale
            $sale = Sale::create([...]);

            // Add items
            foreach ($data['items'] as $item) {
                $sale->items()->create($item);
                $this->inventoryService->deductStock($item);
            }

            // Calculate totals
            $sale->subtotal = $sale->items->sum('total');
            $sale->tax = $this->taxService->calculateTax($sale);
            $sale->discount = $this->discountService->applyDiscount($sale);
            $sale->total = $sale->subtotal + $sale->tax - $sale->discount;
            $sale->save();

            return $sale;
        });
    }
}
```

**Usage in Controller:**
```php
class POSController extends Controller
{
    public function __construct(
        private SaleService $saleService
    ) {}

    public function processSale(ProcessSaleRequest $request)
    {
        $sale = $this->saleService->processSale(
            $request->validated()
        );

        return response()->json($sale);
    }
}
```

### 2. Observer Pattern

**Purpose**: Automatically respond to model events.

**Implementation:**
```php
// app/Observers/SaleObserver.php
class SaleObserver
{
    public function created(Sale $sale): void
    {
        // Update inventory
        foreach ($sale->items as $item) {
            event(new StockDeducted($item->productVariant, $item->quantity));
        }

        // Award loyalty points
        if ($sale->customer) {
            event(new LoyaltyPointsEarned($sale->customer, $sale->total));
        }

        // Queue receipt email
        if ($sale->customer?->email) {
            SendReceiptEmail::dispatch($sale);
        }
    }
}
```

### 3. Repository Pattern

**Purpose**: Abstract complex queries from services.

**Implementation:**
```php
// app/Repositories/SalesRepository.php
class SalesRepository
{
    public function getSalesByDateRange(
        Carbon $from,
        Carbon $to,
        ?int $storeId = null
    ): Collection {
        return Sale::query()
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->whereBetween('created_at', [$from, $to])
            ->with(['items', 'customer', 'payments'])
            ->get();
    }

    public function getTodaysSalesMetrics(int $storeId): array
    {
        return Sale::where('store_id', $storeId)
            ->whereDate('created_at', today())
            ->selectRaw('
                COUNT(*) as transaction_count,
                SUM(total) as total_sales,
                AVG(total) as average_transaction
            ')
            ->first()
            ->toArray();
    }
}
```

### 4. Strategy Pattern

**Purpose**: Different payment processing strategies.

**Implementation:**
```php
// app/Services/Payment/PaymentStrategyInterface.php
interface PaymentStrategyInterface
{
    public function process(Sale $sale, float $amount): PaymentResult;
    public function refund(Sale $sale, float $amount): RefundResult;
}

// app/Services/Payment/StripePaymentStrategy.php
class StripePaymentStrategy implements PaymentStrategyInterface
{
    public function process(Sale $sale, float $amount): PaymentResult
    {
        $charge = $this->stripe->charges->create([
            'amount' => $amount * 100,
            'currency' => 'usd',
            'source' => $sale->payment_token,
        ]);

        return new PaymentResult(
            success: $charge->status === 'succeeded',
            reference: $charge->id
        );
    }
}

// app/Services/Payment/CashPaymentStrategy.php
class CashPaymentStrategy implements PaymentStrategyInterface
{
    public function process(Sale $sale, float $amount): PaymentResult
    {
        // Cash always succeeds
        return new PaymentResult(
            success: true,
            reference: Str::uuid()
        );
    }
}
```

## Multi-Tenancy Architecture

### Tenant Isolation

**Strategy**: Shared database with `store_id` column.

**Implementation:**
```php
// app/Traits/BelongsToStore.php
trait BelongsToStore
{
    protected static function bootBelongsToStore(): void
    {
        static::addGlobalScope('store', function (Builder $builder) {
            if (auth()->check() && auth()->user()->store_id) {
                $builder->where('store_id', auth()->user()->store_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && !$model->store_id) {
                $model->store_id = auth()->user()->store_id;
            }
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
```

**Usage:**
```php
class Product extends Model
{
    use BelongsToStore;

    // Automatically scoped to user's store
}
```

## Database Design

### Normalization

**3rd Normal Form (3NF)** is followed for most tables to eliminate redundancy.

**Denormalization** is used strategically:
- Sale totals cached on `sales` table (recalculated on change)
- Product stock cached on `product_variants` (updated via transactions)

### Key Relationships

```sql
-- One-to-Many
stores → users (store has many users)
stores → sales (store has many sales)
products → product_variants (product has many variants)
sales → sale_items (sale has many items)

-- Many-to-Many
roles ↔ users (via model_has_roles)
products ↔ suppliers (via purchase_orders)

-- Polymorphic
transactions (payable_type, payable_id) → sales, returns

-- Self-Referencing
categories → categories (parent_id) [nested tree]
```

### Indexing Strategy

**Primary Indexes:**
- All primary keys (id)
- Foreign keys (user_id, store_id, product_id, etc.)

**Secondary Indexes:**
- Search fields (sku, barcode, name)
- Filter fields (status, active, created_at)
- Composite indexes for common queries

**Example:**
```php
$table->index(['store_id', 'created_at']); // For date-filtered queries
$table->index(['sku', 'barcode']); // For product search
$table->unique(['store_id', 'sku']); // Unique SKU per store
```

## Caching Strategy

### Cache Layers

**1. Application Cache (Redis)**
```php
// Products (1 hour)
Cache::remember("products.store.{$storeId}", 3600, function () {
    return Product::with('variants')->get();
});

// Settings (indefinite, invalidate on change)
Cache::rememberForever("settings.store.{$storeId}", function () {
    return Setting::where('store_id', $storeId)->pluck('value', 'key');
});

// Tax Rates (24 hours)
Cache::remember('tax-rates', 86400, function () {
    return TaxRate::where('active', true)->get();
});
```

**2. Query Result Cache**
```php
// Using Laravel's query cache
$sales = DB::table('sales')
    ->where('store_id', $storeId)
    ->whereDate('created_at', today())
    ->remember(300) // 5 minutes
    ->get();
```

**3. HTTP Cache (for API responses)**
```php
return response()
    ->json($products)
    ->setCache(['public' => true, 'max_age' => 600]); // 10 minutes
```

### Cache Invalidation

**Event-Driven Invalidation:**
```php
class ProductObserver
{
    public function updated(Product $product): void
    {
        Cache::forget("products.store.{$product->store_id}");
        Cache::forget("product.{$product->id}");
    }
}
```

## Queue Architecture

### Queue Structure

**Multiple queues for priority:**
```php
// config/queue.php
'connections' => [
    'redis' => [
        'queues' => [
            'high',      // Critical: payment processing
            'default',   // Normal: emails, reports
            'low',       // Background: cleanup, analytics
        ],
    ],
],
```

**Job Examples:**
```php
// High priority
ProcessPayment::dispatch($sale)->onQueue('high');

// Default priority
SendReceiptEmail::dispatch($sale); // No onQueue = default

// Low priority
GenerateMonthlyReport::dispatch()->onQueue('low');
```

### Queue Workers

**Supervisor configuration for multiple workers:**
```ini
[program:pos-high-queue]
command=php artisan queue:work redis --queue=high --tries=3
numprocs=2

[program:pos-default-queue]
command=php artisan queue:work redis --queue=default --tries=3
numprocs=4

[program:pos-low-queue]
command=php artisan queue:work redis --queue=low --tries=1
numprocs=1
```

## Security Architecture

### Authentication Flow

```
┌────────────┐
│   User     │
└──────┬─────┘
       │ 1. Login credentials
       ▼
┌──────────────────┐
│  Auth Controller │
└──────┬───────────┘
       │ 2. Validate credentials
       ▼
┌──────────────────┐
│   Sanctum        │ 3. Generate token
│  (Token Auth)    │
└──────┬───────────┘
       │ 4. Return token
       ▼
┌──────────────────┐
│  Client stores   │
│  token in        │
│  localStorage/   │
│  cookie          │
└──────────────────┘
       │ 5. Include in subsequent requests
       ▼
┌──────────────────┐
│  Sanctum         │ 6. Validate token
│  Middleware      │
└──────┬───────────┘
       │ 7. Attach user to request
       ▼
┌──────────────────┐
│  Controller/     │
│  Resource        │
└──────────────────┘
```

### Authorization Layers

**1. Route Middleware:**
```php
Route::middleware(['auth:sanctum', 'role:cashier'])->group(function () {
    Route::get('/pos', [POSController::class, 'index']);
});
```

**2. Policy Authorization:**
```php
class ProductPolicy
{
    public function update(User $user, Product $product): bool
    {
        return $user->can('update-products')
            && $user->store_id === $product->store_id;
    }
}
```

**3. Nova Authorization:**
```php
class Product extends Resource
{
    public static function authorizedToCreate(Request $request): bool
    {
        return $request->user()->can('create-products');
    }
}
```

### Data Protection

**Encryption:**
```php
// Model attributes
protected $casts = [
    'credit_card_last_four' => 'encrypted',
    'customer_ssn' => 'encrypted',
];
```

**Audit Trail:**
```php
// app/Observers/AuditObserver.php
class AuditObserver
{
    public function updated(Model $model): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'action' => 'updated',
            'old_values' => $model->getOriginal(),
            'new_values' => $model->getChanges(),
            'ip_address' => request()->ip(),
        ]);
    }
}
```

## Performance Optimization

### N+1 Query Prevention

**Bad (N+1):**
```php
$sales = Sale::all(); // 1 query
foreach ($sales as $sale) {
    echo $sale->customer->name; // N queries
}
```

**Good (Eager Loading):**
```php
$sales = Sale::with('customer', 'items.productVariant')->get(); // 3 queries total
```

### Lazy Loading Prevention

**Disable lazy loading in development:**
```php
// app/Providers/AppServiceProvider.php
public function boot(): void
{
    Model::preventLazyLoading(! app()->isProduction());
}
```

### Database Optimization

**Query optimization:**
```php
// Use select() to load only needed columns
Product::select('id', 'name', 'sku')->get();

// Use chunk() for large datasets
Product::chunk(1000, function ($products) {
    foreach ($products as $product) {
        // Process
    }
});

// Use cursor() for memory efficiency
Product::cursor()->each(function ($product) {
    // Process
});
```

## Testing Strategy

### Test Pyramid

```
           ▲
          ╱ ╲
         ╱ E2E╲         ← Few (Browser tests with Dusk)
        ╱───────╲
       ╱ Feature ╲      ← More (API/Feature tests)
      ╱───────────╲
     ╱    Unit     ╲    ← Most (Service/Model tests)
    ╱───────────────╲
```

### Test Coverage

**Unit Tests (90% coverage):**
- Services (business logic)
- Calculation methods
- Helpers and utilities

**Feature Tests (80% coverage):**
- API endpoints
- Sale processing flow
- Refund workflow
- Inventory updates

**Browser Tests (critical flows only):**
- POS checkout process
- Nova admin operations

## Deployment Architecture

### Production Setup

```
                    ┌─────────────┐
                    │   Nginx     │
                    │  (Reverse   │
                    │   Proxy)    │
                    └──────┬──────┘
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
        ▼                  ▼                  ▼
┌───────────────┐  ┌───────────────┐  ┌───────────────┐
│  Laravel App  │  │  Laravel App  │  │  Laravel App  │
│   Instance 1  │  │   Instance 2  │  │   Instance 3  │
└───────┬───────┘  └───────┬───────┘  └───────┬───────┘
        │                  │                  │
        └──────────────────┼──────────────────┘
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
        ▼                  ▼                  ▼
┌───────────────┐  ┌───────────────┐  ┌───────────────┐
│     MySQL     │  │     Redis     │  │   Queue       │
│   (Primary)   │  │ (Cache/Session)│  │   Workers     │
└───────────────┘  └───────────────┘  └───────────────┘
```

### Scaling Considerations

**Horizontal Scaling:**
- Multiple Laravel instances behind load balancer
- Stateless sessions (Redis)
- Shared database (MySQL with read replicas)
- CDN for static assets

**Vertical Scaling:**
- Increase server resources
- Optimize database (indexes, query optimization)
- Implement aggressive caching

## Monitoring & Logging

### Application Monitoring

**Laravel Horizon** (Queue monitoring):
- Real-time queue metrics
- Failed job tracking
- Job throughput

**Laravel Telescope** (Development debugging):
- Request/response logging
- Query logging
- Exception tracking

**Production Logging:**
```php
// Structured logging
Log::info('Sale processed', [
    'sale_id' => $sale->id,
    'user_id' => auth()->id(),
    'total' => $sale->total,
    'duration_ms' => $duration,
]);
```

### Performance Monitoring

**Key metrics to track:**
- Average response time
- Database query time
- Queue wait time
- Cache hit rate
- Error rate
- Uptime

## Disaster Recovery

### Backup Strategy

**Automated backups:**
- Database: Daily full backup + hourly incrementals
- Files: Daily backup to S3
- Retention: 30 days rolling

**Recovery procedures:**
- Database restore: < 15 minutes
- Full system restore: < 1 hour
- Tested quarterly

### High Availability

**Redundancy:**
- Database: Master-slave replication
- Redis: Redis Sentinel for automatic failover
- Application: Multiple instances

**Failover:**
- Automatic database failover
- Health checks on all services
- Alerting on failures

---

This architecture is designed to be:
- **Scalable**: Handle growth from single store to hundreds
- **Maintainable**: Clean separation of concerns
- **Testable**: High test coverage with automated testing
- **Secure**: Multiple layers of security
- **Performant**: Optimized for speed at every layer
- **Resilient**: Fault-tolerant with automatic recovery
