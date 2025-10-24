# Laravel + Nova POS System - Development Prompt

## Project Overview
Build a complete Point of Sale (POS) system using Laravel 12.x and Laravel Nova 5.x. The system should handle retail operations including sales transactions, inventory management, customer management, and reporting.

**Important:** This system uses Laravel Nova 5 which features a completely rewritten interface powered by Inertia.js and Vue 3. Refer to the official documentation at https://nova.laravel.com/docs/v5 for implementation details.

## Technical Stack Requirements
- **Framework**: Laravel 12.x
- **Admin Panel**: Laravel Nova 5.x (latest version)
- **Database**: MySQL 8.0+
- **PHP**: 8.3+
- **Frontend**: Livewire 3.x for POS interface
- **Authentication**: Laravel Sanctum
- **Queue System**: Redis
- **Cache**: Redis
- **Payment Processing**: Stripe integration
- **Receipt Printing**: Browser print API
- **Barcode Scanning**: HTML5 barcode scanner integration

## Core Features Required

### 1. Multi-Tenant Architecture
- Support multiple stores/branches
- Tenant isolation at database level
- Central management dashboard
- Per-tenant configuration settings

### 2. User Management & Roles
**Roles to implement:**
- Super Admin (full system access)
- Store Manager (store-level management)
- Cashier (POS operations only)
- Inventory Manager (stock management)
- Accountant (read-only reports)

**Permissions required:**
- Manage users
- Process sales
- Manage inventory
- View reports
- Manage customers
- Process refunds
- Adjust prices
- Access settings

### 3. Product & Inventory Management
- Product categories and subcategories
- Product variants (size, color, etc.)
- SKU and barcode management
- Stock tracking (real-time)
- Low stock alerts
- Reorder point management
- Supplier management
- Purchase orders
- Stock adjustments
- Product images (multiple)
- Bulk import/export (CSV, Excel)

### 4. POS Transaction Interface
- Fast product search (barcode, name, SKU)
- Shopping cart management
- Multiple payment methods:
  - Cash
  - Credit/Debit Card
  - Digital wallets
  - Store credit
  - Split payments
- Customer selection (optional)
- Apply discounts (percentage/fixed)
- Apply coupons
- Tax calculation (configurable rates)
- Print receipt
- Email receipt
- Hold/Park sales
- Quick keys for common products
- Keyboard shortcuts for faster operation

### 5. Customer Management
- Customer profiles (name, email, phone, address)
- Purchase history
- Loyalty points system
- Store credit management
- Customer groups (wholesale, retail, VIP)
- Customer-specific pricing
- Export customer data

### 6. Sales & Returns
- Process sales transactions
- Refund/return management
- Exchange processing
- Partial refunds
- Return reasons tracking
- Refund approval workflow
- Transaction history
- Receipt reprinting

### 7. Reporting & Analytics
**Sales Reports:**
- Daily sales summary
- Sales by product
- Sales by category
- Sales by cashier
- Sales by payment method
- Hourly sales analysis
- Comparative period reports

**Inventory Reports:**
- Current stock levels
- Stock movement
- Low stock items
- Dead stock analysis
- Inventory valuation
- Reorder recommendations

**Financial Reports:**
- Revenue reports
- Profit margins
- Tax reports
- Payment method breakdown
- Expense tracking

**Export formats:** PDF, Excel, CSV

### 8. Cash Management
- Open/close cash drawer
- Cash drawer reconciliation
- Shift management
- Expected vs actual cash
- Cash in/out tracking
- Denomination counting
- Shift reports

### 9. Discounts & Promotions
- Percentage discounts
- Fixed amount discounts
- Buy X Get Y promotions
- Bundle pricing
- Time-based promotions
- Customer group discounts
- Coupon code system
- Automatic discount rules

### 10. Settings & Configuration
- Store information
- Tax settings (multiple tax rates)
- Receipt customization
- Email templates
- Currency settings
- Language settings
- Printer configuration
- Backup settings
- Integration settings

## Database Schema

### Core Tables Required

```sql
-- Users & Authentication
users (id, name, email, password, role, store_id, active, timestamps)
roles (id, name, guard_name, timestamps)
permissions (id, name, guard_name, timestamps)
model_has_roles (role_id, model_type, model_id)
model_has_permissions (permission_id, model_type, model_id)

-- Stores/Branches
stores (id, name, code, address, phone, email, tax_number, active, settings, timestamps)

-- Products & Inventory
categories (id, parent_id, name, slug, description, image, active, timestamps)
products (id, name, slug, sku, barcode, description, category_id, brand_id, active, timestamps)
product_variants (id, product_id, name, sku, barcode, price, cost, stock, image, timestamps)
product_images (id, product_id, image_path, is_primary, sort_order, timestamps)
brands (id, name, slug, logo, active, timestamps)

-- Stock Management
stock_movements (id, product_variant_id, store_id, type, quantity, reference, user_id, notes, timestamps)
stock_adjustments (id, store_id, reference, reason, approved_by, status, notes, timestamps)
stock_adjustment_items (id, adjustment_id, product_variant_id, quantity_before, quantity_after, difference)

-- Suppliers & Purchases
suppliers (id, name, email, phone, address, tax_number, active, timestamps)
purchase_orders (id, supplier_id, store_id, reference, order_date, expected_date, status, total, notes, timestamps)
purchase_order_items (id, purchase_order_id, product_variant_id, quantity, unit_cost, total, received_quantity)

-- Customers
customers (id, name, email, phone, address, customer_group_id, loyalty_points, store_credit, timestamps)
customer_groups (id, name, discount_percentage, active, timestamps)

-- Sales & Transactions
sales (id, store_id, user_id, customer_id, reference, subtotal, tax, discount, total, status, notes, timestamps)
sale_items (id, sale_id, product_variant_id, quantity, unit_price, discount, tax, total, timestamps)
sale_payments (id, sale_id, payment_method_id, amount, reference, timestamps)

-- Returns & Refunds
returns (id, sale_id, store_id, user_id, reference, reason, subtotal, tax, total, status, timestamps)
return_items (id, return_id, sale_item_id, quantity, unit_price, total, timestamps)

-- Payments
payment_methods (id, name, type, active, settings, timestamps)
transactions (id, transactionable_type, transactionable_id, type, amount, reference, timestamps)

-- Discounts & Promotions
discounts (id, name, type, value, start_date, end_date, min_amount, max_uses, active, timestamps)
coupons (id, code, discount_id, uses, max_uses, expires_at, active, timestamps)

-- Cash Management
cash_drawers (id, store_id, user_id, opened_at, closed_at, opening_cash, expected_cash, actual_cash, difference, status)
cash_transactions (id, cash_drawer_id, type, amount, reference, notes, timestamps)

-- Settings
settings (id, store_id, key, value, timestamps)
tax_rates (id, name, rate, active, timestamps)

-- Receipts
receipts (id, sale_id, template, printed_at, emailed_at, timestamps)
```

## Laravel Nova Configuration

### Nova 5 Specific Considerations

**Key Changes in Nova 5:**
- Built on Inertia.js and Vue 3 (replaced Vue 2)
- Improved performance with better reactivity
- Enhanced search with better performance
- New field types and improvements
- Better resource organization
- Streamlined authorization
- Improved customization capabilities

**Custom Components (Inertia/Vue 3):**
When creating custom cards, fields, or tools for Nova 5:
- Use Vue 3 Composition API
- Leverage Inertia.js for seamless navigation
- Follow Nova 5's component structure
- Use TypeScript for better type safety (optional but recommended)

**Nova 5 Authorization:**
```php
// Use the simplified authorization in Nova 5
public static function authorizedToCreate(Request $request)
{
    return $request->user()->can('create-products');
}
```

### 1. Nova Resources to Create

**User Management:**
- User Resource (with role assignment)
- Role Resource
- Permission Resource

**Store Management:**
- Store Resource
- StoreSettings Resource

**Products:**
- Product Resource (with variants, images)
- ProductVariant Resource
- Category Resource (with nested tree)
- Brand Resource

**Inventory:**
- StockMovement Resource (read-only)
- StockAdjustment Resource
- PurchaseOrder Resource
- Supplier Resource

**Sales:**
- Sale Resource (read-only, detailed view)
- Return Resource

**Customers:**
- Customer Resource
- CustomerGroup Resource

**Discounts:**
- Discount Resource
- Coupon Resource

**Reports:**
- Custom reports dashboard
- Sales analytics cards
- Inventory metrics cards

**Settings:**
- TaxRate Resource
- PaymentMethod Resource
- Setting Resource

### 2. Nova Features to Implement

**Nova v5 Enhancements to Utilize:**
- Inertia.js-powered interface (Vue 3)
- Improved performance and reactivity
- Enhanced search capabilities
- Better resource organization
- Modern UI components

**Custom Fields:**
- BarcodeField (with scanner integration)
- CurrencyField
- PercentageField
- ImageGalleryField
- StatusBadgeField
- ColorField
- TagField
- KeyValueField (for product attributes)

**Custom Filters:**
- DateRangeFilter
- StoreFilter
- CategoryFilter
- StatusFilter
- PaymentMethodFilter
- UserFilter
- PriceRangeFilter

**Actions:**
- ExportToExcel
- ExportToPDF
- BulkPriceUpdate
- BulkStockUpdate
- ApproveStockAdjustment
- ProcessRefund
- SendReceipt
- PrintBarcode
- ActivateDeactivate
- BulkCategoryAssignment

**Lenses:**
- LowStockProducts
- BestSellingProducts
- TodaysSales
- PendingReturns
- ExpiringSoonProducts
- NegativeStockItems

**Metrics:**
- TodaysSalesValue (Value)
- TransactionsToday (Value)
- LowStockItems (Value)
- TodaysProfit (Trend)
- SalesByPaymentMethod (Partition)
- TopSellingCategories (Partition)
- AverageTransactionValue (Value)
- CustomerGrowth (Trend)

**Dashboards:**
- Main Dashboard (sales overview)
- Inventory Dashboard (stock management)
- Reports Dashboard (analytics)
- Store Performance Dashboard

**Cards:**
- SalesOverview (custom Inertia card)
- InventoryStatus (custom Inertia card)
- QuickStats (custom Inertia card)
- RecentTransactions (custom card)
- TopProducts (custom card)

## Livewire POS Interface

### Components to Build

**Main POS Screen:**
```
POSInterface.php
├── ProductSearch.php (barcode/name search)
├── ShoppingCart.php (items in cart)
├── CustomerSelect.php (optional customer)
├── PaymentModal.php (payment processing)
├── DiscountModal.php (apply discounts)
└── OnHoldSales.php (parked sales)
```

**Features:**
- Real-time product search with debounce
- Keyboard shortcuts (F1-F12 for common actions)
- Touch-friendly UI for tablets
- Responsive design
- Offline mode support (with sync)
- Barcode scanner integration
- Receipt printing
- Split payment support
- Quick product buttons

### POS Routes
```php
Route::middleware(['auth:sanctum', 'role:cashier'])->group(function () {
    Route::get('/pos', POSInterface::class)->name('pos');
    Route::post('/pos/hold', [POSController::class, 'holdSale']);
    Route::post('/pos/retrieve', [POSController::class, 'retrieveSale']);
    Route::post('/pos/process', [POSController::class, 'processSale']);
    Route::post('/pos/print-receipt', [POSController::class, 'printReceipt']);
});
```

## API Endpoints for POS

### RESTful API (for potential mobile app)
```
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/products/search?q={query}
GET    /api/products/{id}
GET    /api/customers/search?q={query}
POST   /api/sales
POST   /api/sales/{id}/refund
GET    /api/sales/{id}
POST   /api/cash-drawer/open
POST   /api/cash-drawer/close
GET    /api/reports/sales/today
```

## Business Logic Requirements

### 1. Sale Processing Flow
1. Scan/search products
2. Add to cart with quantity
3. Apply customer (optional)
4. Apply discounts/coupons
5. Calculate tax
6. Select payment method(s)
7. Process payment
8. Update inventory
9. Record transaction
10. Print receipt
11. Award loyalty points

### 2. Inventory Management Rules
- Deduct stock on sale
- Add stock on return
- Log all stock movements
- Prevent negative stock (configurable)
- Alert on low stock
- Support stock transfers between stores
- Track stock by batch/lot (optional)

### 3. Pricing Rules
- Base price from product_variant
- Customer group discount applied
- Promotion discount applied
- Manual discount (with permission)
- Tax calculated on final price
- Rounding rules applied

### 4. Return Policy
- Return within X days (configurable)
- Require original receipt
- Partial returns allowed
- Return to stock or mark as damaged
- Refund to original payment method
- Store credit option
- Manager approval required for refunds > X amount

### 5. Cash Drawer Rules
- Must open drawer at shift start
- Must close drawer at shift end
- Track all cash in/out
- Reconciliation report
- Over/short reporting
- Multi-drawer support per store

## Security Requirements

### 1. Authentication & Authorization
- Two-factor authentication (optional)
- Password policies
- Session management
- Role-based access control (RBAC)
- Permission-based feature access
- API token authentication

### 2. Data Security
- Encrypt sensitive data (payment info, PII)
- Secure payment processing (PCI DSS)
- SQL injection prevention (Eloquent)
- XSS prevention
- CSRF protection
- Rate limiting on API

### 3. Audit Trail
- Log all sales transactions
- Log inventory changes
- Log user actions (who, what, when)
- Log refunds and voids
- Log price changes
- Log settings changes

### 4. Backup & Recovery
- Automated daily backups
- Point-in-time recovery
- Transaction log backup
- Backup verification
- Disaster recovery plan

## Performance Requirements

### 1. Optimization
- Database indexing on frequently queried fields
- Query optimization (N+1 prevention)
- Cache frequently accessed data (products, settings)
- Lazy loading for large datasets
- Asset optimization (CSS, JS minification)
- Image optimization

### 2. Scalability
- Queue long-running tasks (reports, exports)
- Horizontal scaling support
- Load balancing ready
- CDN for static assets
- Database read replicas

### 3. POS Speed Requirements
- Product search: < 200ms
- Add to cart: < 100ms
- Process payment: < 2s
- Print receipt: < 3s
- Load POS interface: < 1s

## Testing Requirements

### 1. Feature Tests
- Test sale processing flow
- Test refund processing
- Test inventory updates
- Test discount calculations
- Test tax calculations
- Test payment processing
- Test cash drawer operations
- Test user permissions

### 2. Unit Tests
- Test models and relationships
- Test service classes
- Test calculation methods
- Test validation rules
- Test helper functions

### 3. Browser Tests (Dusk)
- Test POS interface workflow
- Test Nova admin operations
- Test receipt printing
- Test barcode scanning

## Installation & Setup Instructions

### 1. Initial Setup Commands
```bash
# Create Laravel 12 project
composer create-project laravel/laravel pos-system
cd pos-system

# Install Nova 5
# First, add the Nova repository to composer.json
composer config repositories.nova '{"type": "composer", "url": "https://nova.laravel.com"}' --file composer.json

# Then require Nova (you'll need your license key)
composer require laravel/nova

# Install required packages
composer require spatie/laravel-permission
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
composer require livewire/livewire
composer require laravel/sanctum

# Publish Nova assets and install
php artisan nova:install
php artisan migrate

# Setup database
php artisan db:seed

# Create Nova resources
php artisan nova:resource Product
php artisan nova:resource Sale
php artisan nova:resource Customer
php artisan nova:resource Category
# ... etc

# Create Nova actions
php artisan nova:action ExportToExcel
php artisan nova:action ProcessRefund
# ... etc

# Create Nova filters
php artisan nova:filter DateRangeFilter
php artisan nova:filter StoreFilter
# ... etc

# Create Nova metrics
php artisan nova:metric TodaysSalesValue
php artisan nova:metric TransactionsToday
# ... etc

# Setup storage
php artisan storage:link

# Compile assets (Vite for Laravel 12)
npm install
npm run build
```

### 2. Environment Variables
```env
APP_NAME="POS System"
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_db
DB_USERNAME=root
DB_PASSWORD=

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525

STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret

RECEIPT_LOGO_PATH=storage/receipt-logo.png
RECEIPT_FOOTER_TEXT="Thank you for your business!"

# Nova License
NOVA_LICENSE_KEY=your_nova_license_key
```

### 3. Vite Configuration (Laravel 12)
Ensure your `vite.config.js` includes Nova's paths:
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
```

## Development Workflow

### Phase 1: Foundation (Week 1)
1. Setup Laravel & Nova
2. Create database migrations
3. Setup authentication
4. Configure roles & permissions
5. Create base models and relationships

### Phase 2: Nova Admin (Week 2)
1. Create all Nova resources
2. Configure fields and relationships
3. Implement filters and actions
4. Create custom metrics and cards
5. Setup validation rules

### Phase 3: POS Interface (Week 2-3)
1. Build Livewire components
2. Implement product search
3. Build shopping cart
4. Payment processing
5. Receipt generation
6. Barcode scanner integration

### Phase 4: Business Logic (Week 3-4)
1. Sale processing service
2. Inventory management service
3. Discount calculation service
4. Tax calculation service
5. Cash drawer management
6. Return/refund processing

### Phase 5: Reporting (Week 4)
1. Sales reports
2. Inventory reports
3. Financial reports
4. Export functionality
5. Dashboard analytics

### Phase 6: Testing & Optimization (Week 5)
1. Write comprehensive tests
2. Performance optimization
3. Security audit
4. Bug fixes
5. Documentation

## Code Organization

### Directory Structure
```
app/
├── Models/
│   ├── Product.php
│   ├── Sale.php
│   ├── Customer.php
│   └── ...
├── Nova/
│   ├── Resources/
│   │   ├── Product.php
│   │   ├── Sale.php
│   │   └── ...
│   ├── Metrics/
│   │   ├── TodaysSalesValue.php
│   │   ├── TransactionsToday.php
│   │   └── ...
│   ├── Filters/
│   │   ├── DateRangeFilter.php
│   │   ├── StoreFilter.php
│   │   └── ...
│   ├── Actions/
│   │   ├── ExportToExcel.php
│   │   ├── ProcessRefund.php
│   │   └── ...
│   ├── Lenses/
│   │   ├── LowStockProducts.php
│   │   └── ...
│   ├── Dashboards/
│   │   ├── Main.php
│   │   ├── Inventory.php
│   │   └── ...
│   └── Cards/
│       └── SalesOverview.php
├── Services/
│   ├── SaleService.php
│   ├── InventoryService.php
│   ├── DiscountService.php
│   └── TaxService.php
├── Http/
│   ├── Controllers/
│   │   ├── POSController.php
│   │   └── API/
│   ├── Livewire/
│   │   ├── POSInterface.php
│   │   └── ...
│   └── Middleware/
├── Observers/
│   ├── SaleObserver.php
│   └── StockMovementObserver.php
└── Traits/
    ├── HasTenant.php
    └── Searchable.php

database/
├── migrations/
├── seeders/
│   ├── RoleSeeder.php
│   ├── UserSeeder.php
│   └── ProductSeeder.php
└── factories/

resources/
├── views/
│   ├── livewire/
│   │   └── pos-interface.blade.php
│   └── receipts/
│       └── standard.blade.php
├── js/
│   ├── app.js
│   └── pos/
│       ├── barcode-scanner.js
│       └── components/ (Vue 3 components for custom Nova elements)
└── css/
    └── app.css

nova-components/ (for custom Nova 5 Inertia components)
└── SalesOverviewCard/
    ├── src/
    │   └── SalesOverviewCard.vue
    └── resources/
        └── js/
            └── components/

tests/
├── Feature/
│   ├── SaleProcessingTest.php
│   ├── InventoryManagementTest.php
│   └── ...
└── Unit/
    ├── DiscountServiceTest.php
    └── ...
```

## Key Classes to Implement

### 1. Services
```php
// app/Services/SaleService.php
class SaleService
{
    public function createSale(array $data): Sale
    public function calculateTotal(Sale $sale): float
    public function processSale(Sale $sale, array $payments): void
    public function voidSale(Sale $sale, string $reason): void
}

// app/Services/InventoryService.php
class InventoryService
{
    public function adjustStock(ProductVariant $variant, int $quantity, string $type): void
    public function checkAvailability(ProductVariant $variant, int $quantity): bool
    public function transferStock(ProductVariant $variant, Store $from, Store $to, int $quantity): void
}

// app/Services/DiscountService.php
class DiscountService
{
    public function applyDiscount(Sale $sale, Discount $discount): void
    public function calculateDiscountAmount(float $subtotal, Discount $discount): float
    public function validateCoupon(string $code): ?Coupon
}
```

### 2. Observers
```php
// app/Observers/SaleObserver.php
class SaleObserver
{
    public function created(Sale $sale): void
    {
        // Update inventory
        // Create transaction record
        // Award loyalty points
    }
}
```

### 3. Jobs
```php
// app/Jobs/GenerateSalesReport.php
class GenerateSalesReport implements ShouldQueue
{
    // Generate and email sales report
}

// app/Jobs/SendReceiptEmail.php
class SendReceiptEmail implements ShouldQueue
{
    // Send receipt via email
}
```

## Creating Custom Nova 5 Components

### Custom Field Example (Barcode Field)
Nova 5 uses Inertia.js and Vue 3 for custom components.

**1. Create the field:**
```bash
php artisan nova:field BarcodeField
```

**2. Implement the Vue 3 component:**
```vue
<!-- resources/js/components/BarcodeField.vue -->
<script setup>
import { ref } from 'vue'

const props = defineProps({
  field: Object,
  value: String,
})

const emit = defineEmits(['update:value'])

const barcode = ref(props.value)

const handleBarcodeScanner = (event) => {
  // Handle barcode scanner input
  if (event.key === 'Enter') {
    emit('update:value', barcode.value)
  }
}
</script>

<template>
  <div class="barcode-field">
    <input
      v-model="barcode"
      @keypress="handleBarcodeScanner"
      type="text"
      class="form-control form-input form-input-bordered"
      :placeholder="field.placeholder"
    />
  </div>
</template>
```

**3. Register the field in Nova:**
```php
// app/Nova/Fields/BarcodeField.php
namespace App\Nova\Fields;

use Laravel\Nova\Fields\Field;

class BarcodeField extends Field
{
    public $component = 'barcode-field';
    
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);
    }
}
```

### Custom Card Example (Sales Overview)
**1. Create the card:**
```bash
php artisan nova:card SalesOverviewCard
```

**2. Build the Inertia/Vue 3 component:**
```vue
<!-- nova-components/SalesOverviewCard/resources/js/components/Card.vue -->
<script setup>
import { ref, onMounted } from 'vue'

const salesData = ref({
  today: 0,
  week: 0,
  month: 0,
  year: 0
})

const fetchSalesData = async () => {
  const response = await Nova.request().get('/nova-vendor/sales-overview/data')
  salesData.value = response.data
}

onMounted(() => {
  fetchSalesData()
  // Refresh every 30 seconds
  setInterval(fetchSalesData, 30000)
})
</script>

<template>
  <div class="card">
    <div class="p-6">
      <h3 class="text-base font-bold mb-4">Sales Overview</h3>
      <div class="grid grid-cols-2 gap-4">
        <div class="border rounded-lg p-4">
          <div class="text-gray-500 text-sm">Today</div>
          <div class="text-2xl font-bold">${{ salesData.today.toLocaleString() }}</div>
        </div>
        <div class="border rounded-lg p-4">
          <div class="text-gray-500 text-sm">This Week</div>
          <div class="text-2xl font-bold">${{ salesData.week.toLocaleString() }}</div>
        </div>
        <div class="border rounded-lg p-4">
          <div class="text-gray-500 text-sm">This Month</div>
          <div class="text-2xl font-bold">${{ salesData.month.toLocaleString() }}</div>
        </div>
        <div class="border rounded-lg p-4">
          <div class="text-gray-500 text-sm">This Year</div>
          <div class="text-2xl font-bold">${{ salesData.year.toLocaleString() }}</div>
        </div>
      </div>
    </div>
  </div>
</template>
```

## Additional Features (Optional/Future)

### Advanced Features
- Multi-currency support
- Offline POS mode with sync
- Kitchen display system (for restaurants)
- Table management (for restaurants)
- Appointment booking (for services)
- Layaway/installment payments
- Gift card management
- Employee time tracking
- Commission tracking
- Advanced analytics (ML-based insights)
- Mobile app (React Native)
- Digital signage integration
- Scale/weight integration
- Label printer integration

### Integrations
- QuickBooks integration
- Xero integration
- Mailchimp integration
- SMS notifications (Twilio)
- WhatsApp notifications
- Accounting software sync
- E-commerce platform sync (WooCommerce, Shopify)

## Delivery Expectations

### Code Quality Standards
- Follow PSR-12 coding standards
- Use PHP 8.3+ features (typed properties, named arguments, readonly classes, typed class constants)
- Utilize Laravel 12 features (model casts improvements, improved validation)
- Implement proper error handling
- Write descriptive comments
- Use meaningful variable names
- Follow Laravel best practices
- Implement service layer pattern
- Use repository pattern for complex queries
- Leverage Nova 5's Inertia.js capabilities for custom components

### Documentation Requirements
- API documentation (Postman collection)
- Setup instructions (README.md)
- User manual (for cashiers)
- Admin manual (for managers)
- Code documentation (PHPDoc)
- Database schema diagram

### Testing Coverage
- Minimum 80% code coverage
- All critical paths tested
- Edge cases covered
- Performance benchmarks documented

## Success Criteria

The POS system is complete when:
1. ✅ All core features are implemented and working
2. ✅ Nova admin panel is fully functional
3. ✅ POS interface is fast and responsive
4. ✅ All tests pass with >80% coverage
5. ✅ Security requirements are met
6. ✅ Performance benchmarks are achieved
7. ✅ Documentation is complete
8. ✅ System can handle 1000+ products
9. ✅ System can process 100+ transactions/hour
10. ✅ Multi-store setup works correctly

## Important Notes

### Nova 5 Specific Resources
- **Official Documentation**: https://nova.laravel.com/docs/v5
- **Key Changes from Nova 4**:
  - Complete UI rewrite with Inertia.js and Vue 3
  - Improved search performance
  - Enhanced authorization methods
  - Better customization API
  - TypeScript support for custom components
  - New field types and improvements
  
### Installation Requirements
- Active Nova license required
- Nova license key must be set in `.env` file
- Composer authentication required for Nova repository
- Node.js 18+ for Vue 3 compilation

### Development Best Practices

1. **Start with migrations first** - Create all database tables before building features
2. **Seed realistic data** - Create factories and seeders for testing
3. **Build Nova resources progressively** - One resource at a time, fully complete
4. **POS UI is critical** - Must be fast, intuitive, and error-free
5. **Test inventory updates thoroughly** - Stock accuracy is paramount
6. **Implement proper transaction handling** - Use database transactions for sales
7. **Cache aggressively** - Products, settings, tax rates should be cached
8. **Log everything important** - Audit trail is crucial for retail
9. **Mobile responsive** - Admin should work on tablets
10. **Print testing** - Test receipt printing thoroughly on actual hardware
11. **Utilize Nova 5's Inertia/Vue 3 stack** - Build reactive, performant custom components
12. **Follow Vue 3 Composition API** - Use modern Vue patterns for Nova customizations

---

## Quick Reference

### Tech Stack Summary
| Component | Technology | Version |
|-----------|-----------|---------|
| Backend Framework | Laravel | 12.x |
| Admin Panel | Laravel Nova | 5.x |
| Database | MySQL | 8.0+ |
| PHP | PHP | 8.3+ |
| Frontend (POS) | Livewire | 3.x |
| Frontend (Nova) | Inertia.js + Vue 3 | Latest |
| Build Tool | Vite | Latest |
| Authentication | Laravel Sanctum | Latest |
| Permissions | Spatie Permission | Latest |
| Queue/Cache | Redis | Latest |
| PDF Generation | DomPDF | Latest |
| Excel Export | Maatwebsite Excel | Latest |

### Key Artisan Commands
```bash
# Nova Resources
php artisan nova:resource ModelName

# Nova Components
php artisan nova:action ActionName
php artisan nova:filter FilterName
php artisan nova:metric MetricName
php artisan nova:card CardName
php artisan nova:field FieldName
php artisan nova:dashboard DashboardName

# Livewire Components
php artisan make:livewire ComponentName

# Database
php artisan migrate
php artisan db:seed
php artisan make:migration migration_name
php artisan make:seeder SeederName
php artisan make:factory FactoryName

# Testing
php artisan test
php artisan test --filter TestName

# Cache & Queue
php artisan cache:clear
php artisan config:clear
php artisan queue:work
```

### Essential Nova 5 Resources
- Installation: https://nova.laravel.com/docs/v5/installation
- Resources: https://nova.laravel.com/docs/v5/resources
- Fields: https://nova.laravel.com/docs/v5/resources/fields
- Actions: https://nova.laravel.com/docs/v5/actions
- Filters: https://nova.laravel.com/docs/v5/filters
- Metrics: https://nova.laravel.com/docs/v5/metrics
- Cards: https://nova.laravel.com/docs/v5/customization/cards
- Authorization: https://nova.laravel.com/docs/v5/resources/authorization

## Development Start Command

When starting development, first:
1. Analyze this entire prompt
2. Ask clarifying questions if needed
3. Create a detailed implementation plan
4. Start with database migrations
5. Build incrementally and test continuously
6. Commit frequently with descriptive messages

Good luck building an excellent POS system! 🚀
