# Laravel + Nova 5 POS System

## Project Overview

This is a comprehensive **Point of Sale (POS) System** built with Laravel 12 and Laravel Nova 5, designed for retail operations. The system provides a complete solution for managing sales transactions, inventory, customers, reporting, and multi-store operations.

### What This Project Is

A full-featured, production-ready POS system that includes:
- **Admin Panel**: Powered by Laravel Nova 5 (Inertia.js + Vue 3) for complete back-office management
- **POS Interface**: Fast, reactive Livewire 3-based cashier interface optimized for speed and ease of use
- **REST API**: Sanctum-authenticated API for mobile apps and third-party integrations
- **Multi-Tenant**: Support for multiple stores/branches with data isolation
- **Payment Processing**: Stripe integration for card payments, plus cash, digital wallets, and split payments
- **Inventory Management**: Real-time stock tracking, purchase orders, supplier management, and automated reorder alerts
- **Customer Management**: Customer profiles, loyalty points, purchase history, and customer groups
- **Reporting & Analytics**: Comprehensive sales, inventory, and financial reports with exports

### What We Want to Achieve

**Primary Goals:**
1. **Speed & Efficiency**: POS interface must be blazingly fast (< 1s load, < 200ms search, < 2s checkout)
2. **Reliability**: 100% accurate inventory tracking with proper transaction handling
3. **Scalability**: Support 1000+ products, 100+ transactions/hour, multiple stores
4. **User-Friendly**: Intuitive interfaces for both cashiers and administrators
5. **Comprehensive**: Complete retail management solution in one platform
6. **Extensible**: Clean architecture for easy customization and feature additions

**Success Metrics:**
- ⚡ POS response time < 200ms for all operations
- 📦 Real-time inventory accuracy (100%)
- 👥 Support multiple concurrent users per store
- 📊 Comprehensive reporting with PDF/Excel export
- 🔒 Secure payment processing (PCI DSS compliant)
- 📱 REST API ready for mobile app integration
- ✅ 80%+ test coverage

## Technical Architecture

### Technology Stack

```
┌─────────────────────────────────────────────────────────────┐
│                     POS System                              │
├─────────────────────────────────────────────────────────────┤
│  Frontend Layer                                             │
│  ├─ Admin Panel:  Laravel Nova 5 (Inertia.js + Vue 3)     │
│  ├─ POS Interface: Livewire 3                              │
│  └─ Build Tool:    Vite                                     │
├─────────────────────────────────────────────────────────────┤
│  Application Layer                                          │
│  ├─ Framework:     Laravel 12.x                            │
│  ├─ PHP Version:   8.3+                                     │
│  ├─ Auth:          Laravel Sanctum (API tokens)            │
│  ├─ Permissions:   Spatie Laravel Permission               │
│  └─ Services:      Custom service layer (business logic)   │
├─────────────────────────────────────────────────────────────┤
│  Data Layer                                                 │
│  ├─ Database:      MySQL 8.0+                              │
│  ├─ Cache:         Redis                                    │
│  ├─ Queue:         Redis                                    │
│  └─ Session:       Redis                                    │
├─────────────────────────────────────────────────────────────┤
│  Integration Layer                                          │
│  ├─ Payments:      Stripe API                              │
│  ├─ PDF:           DomPDF                                   │
│  ├─ Excel:         Maatwebsite Excel                       │
│  └─ Barcode:       HTML5 Scanner Integration               │
└─────────────────────────────────────────────────────────────┘
```

### System Components

#### 1. **Admin Panel (Laravel Nova 5)**
- **Purpose**: Complete back-office management for administrators and managers
- **Features**:
  - 25+ Nova resources (Products, Sales, Customers, Inventory, etc.)
  - Custom fields (Barcode scanner, Currency, Image gallery)
  - Advanced filters (Date range, Store, Category, Status)
  - Bulk actions (Export, Price update, Stock adjustment)
  - Lenses (Low stock, Best sellers, Pending returns)
  - Metrics & Dashboards (Sales overview, Inventory status)
  - Custom Inertia.js/Vue 3 cards for real-time data
- **Technology**: Nova 5.7.6 (Inertia.js + Vue 3 + Tailwind CSS)

#### 2. **POS Interface (Livewire)**
- **Purpose**: Fast, intuitive cashier interface for processing sales
- **Features**:
  - Real-time product search (barcode, name, SKU)
  - Shopping cart with quantity adjustments
  - Multiple payment methods (cash, card, split)
  - Customer selection and loyalty points
  - Discount/coupon application
  - Hold/park sales for later
  - Receipt printing and email
  - Cash drawer management
  - Keyboard shortcuts (F1-F12) for speed
  - Touch-optimized for tablets
  - Offline mode with sync
- **Technology**: Livewire 3 + Alpine.js + Tailwind CSS

#### 3. **REST API (Sanctum)**
- **Purpose**: Mobile app integration and third-party access
- **Endpoints**:
  - Authentication (login, logout, token management)
  - Products (list, search, details)
  - Customers (CRUD operations)
  - Sales (create, retrieve, refund)
  - Cash drawer (open, close, status)
  - Reports (sales, inventory)
- **Technology**: Laravel Sanctum (token-based authentication)

#### 4. **Service Layer**
Clean separation of business logic from controllers:
- `SaleService`: Sale processing, calculation, payment
- `InventoryService`: Stock management, movements, transfers
- `DiscountService`: Discount calculation, coupon validation
- `TaxService`: Tax calculation, rate management
- `PaymentService`: Payment processing (Stripe, cash, etc.)
- `CashDrawerService`: Cash management, reconciliation
- `ReportService`: Report generation, exports

### Database Architecture

**Core Tables (40+ tables):**

```
Users & Auth
├─ users, roles, permissions
├─ model_has_roles, model_has_permissions
└─ personal_access_tokens (Sanctum)

Multi-Tenancy
├─ stores
└─ settings

Products & Catalog
├─ products, product_variants, product_images
├─ categories (nested), brands
└─ attributes, attribute_values

Inventory Management
├─ stock_movements (transaction log)
├─ stock_adjustments, stock_adjustment_items
├─ purchase_orders, purchase_order_items
└─ suppliers

Sales & Transactions
├─ sales, sale_items, sale_payments
├─ returns, return_items
└─ transactions

Customers
├─ customers, customer_groups
├─ loyalty_points_transactions
└─ store_credits

Discounts & Promotions
├─ discounts, coupons
└─ discount_usage

Cash Management
├─ cash_drawers
└─ cash_transactions

Configuration
├─ payment_methods
├─ tax_rates
└─ receipts
```

**Key Relationships:**
- Multi-tenant isolation via `store_id` on relevant tables
- Polymorphic relations for transactions
- Nested set for category tree
- Many-to-many for roles/permissions

### Security Architecture

1. **Authentication**
   - Session-based for web (Nova + POS)
   - Token-based for API (Sanctum)
   - Two-factor authentication support

2. **Authorization**
   - Role-Based Access Control (RBAC) via Spatie Permission
   - Roles: Super Admin, Store Manager, Cashier, Inventory Manager, Accountant
   - Nova policies for resource-level permissions
   - Tenant isolation (users see only their store data)

3. **Data Security**
   - Encrypted sensitive data (payment info, PII)
   - CSRF protection (Laravel default)
   - XSS prevention (Blade escaping)
   - SQL injection prevention (Eloquent ORM)
   - Rate limiting on API endpoints

4. **Audit Trail**
   - All sales transactions logged
   - Stock movements tracked with user/reason
   - User actions logged (who, what, when)
   - Price changes logged

## Core Features

### 1. Multi-Tenant Support
- Multiple stores/branches in single installation
- Data isolation at database level
- Per-store configuration and settings
- Central management dashboard for super admin

### 2. User Roles & Permissions
**Roles:**
- **Super Admin**: Full system access, manage all stores
- **Store Manager**: Manage assigned store, view reports, approve refunds
- **Cashier**: Process sales, returns, cash drawer (POS only)
- **Inventory Manager**: Manage products, stock, purchase orders
- **Accountant**: Read-only access to reports and financial data

**Permissions:**
- Granular permissions (manage-users, process-sales, manage-inventory, etc.)
- Permission-based UI (hide features user can't access)
- Nova authorization policies

### 3. Product & Inventory Management
- Unlimited products with variants (size, color, etc.)
- SKU and barcode management
- Multiple product images
- Nested categories and tags
- Real-time stock tracking
- Low stock alerts and reorder points
- Supplier management
- Purchase orders with receiving
- Stock adjustments with approval workflow
- Stock transfers between stores
- Bulk import/export (CSV, Excel)

### 4. POS Transaction Interface
- Lightning-fast product search (< 200ms)
- Barcode scanning support
- Quick product buttons (F1-F12)
- Shopping cart with easy quantity adjustment
- Customer selection (optional, with loyalty points)
- Multiple payment methods:
  - Cash (with change calculation)
  - Credit/Debit cards (Stripe)
  - Digital wallets
  - Store credit
  - Split payments (multiple methods per sale)
- Discount application (percentage/fixed/coupon)
- Tax calculation (configurable rates)
- Receipt printing (browser API)
- Email receipts
- Hold/park sales for later
- Keyboard shortcuts for speed
- Offline mode with sync

### 5. Customer Management
- Customer profiles (name, email, phone, address)
- Purchase history (complete transaction log)
- Loyalty points system (earn points on purchases)
- Store credit management
- Customer groups (Wholesale, Retail, VIP) with custom pricing
- Export customer data

### 6. Sales & Returns
- Process sales with multiple payment methods
- Refund/return management with reasons
- Partial returns support
- Exchange processing
- Return approval workflow (manager approval)
- Transaction history and audit trail
- Receipt reprinting

### 7. Cash Management
- Open/close cash drawer workflow
- Shift management
- Cash drawer reconciliation (expected vs actual)
- Over/short reporting
- Cash in/out tracking (expenses, bank deposits)
- Denomination counting
- Shift reports

### 8. Discounts & Promotions
- Percentage discounts
- Fixed amount discounts
- Buy X Get Y promotions
- Bundle pricing
- Time-based promotions (happy hour)
- Customer group discounts
- Coupon code system with usage limits
- Automatic discount rules

### 9. Reporting & Analytics
**Sales Reports:**
- Daily sales summary
- Sales by product/category
- Sales by cashier/store
- Sales by payment method
- Hourly sales analysis
- Comparative period reports

**Inventory Reports:**
- Current stock levels
- Stock movement history
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

**Export Formats:** PDF, Excel, CSV

### 10. System Configuration
- Store information (name, address, tax number)
- Tax settings (multiple tax rates)
- Receipt customization (logo, footer text)
- Email templates
- Currency settings
- Payment method configuration
- Printer settings
- Backup settings

## Performance Requirements

### Speed Targets
| Operation | Target | Strategy |
|-----------|--------|----------|
| POS interface load | < 1s | Vite optimization, lazy loading |
| Product search | < 200ms | Database indexes, Redis cache |
| Add to cart | < 100ms | Livewire optimization, AlpineJS |
| Process payment | < 2s | Service layer, database transactions |
| Print receipt | < 3s | Optimized templates, browser print API |

### Optimization Strategies
1. **Database**:
   - Indexes on frequently queried fields (SKU, barcode, name, store_id, created_at)
   - Query optimization (N+1 prevention with eager loading)
   - Read replicas for reports (future scaling)

2. **Caching** (Redis):
   - Products (cache for 1 hour, invalidate on update)
   - Settings (cache indefinitely, invalidate on change)
   - Tax rates (cache for 24 hours)
   - Category tree (cache for 1 hour)
   - User permissions (cache for session)

3. **Queue** (Redis):
   - Long-running tasks (report generation, exports)
   - Email sending (receipts, alerts)
   - Bulk operations (price updates, stock adjustments)
   - Scheduled tasks (daily reports, low stock alerts)

4. **Frontend**:
   - Asset minification and bundling (Vite)
   - Lazy loading components
   - Image optimization (responsive images, WebP)
   - CDN for static assets (production)

## Development Standards

### Code Quality Standards

1. **PHP Standards**
   - Follow PSR-12 coding style
   - Use PHP 8.3+ features:
     - Typed properties and return types
     - Readonly classes/properties
     - Enums for constants
     - Named arguments
     - Null safe operator (?->)
   - Comprehensive PHPDoc for all classes/methods
   - Meaningful variable and method names

2. **Laravel Best Practices**
   - Service layer for business logic (keep controllers thin)
   - Repository pattern for complex queries
   - Eloquent ORM (no raw queries unless necessary)
   - Form requests for validation
   - Resource classes for API responses
   - Observers for model events
   - Queue jobs for long-running tasks
   - Proper error handling and logging

3. **Nova 5 Best Practices**
   - Use Inertia.js and Vue 3 for custom components
   - Follow Vue 3 Composition API
   - Leverage Nova's built-in fields before creating custom
   - Proper authorization with policies
   - Use actions for bulk operations
   - Lenses for filtered views
   - Metrics for dashboard stats

4. **Livewire Best Practices**
   - Small, focused components (< 500 lines)
   - Debounce for search inputs
   - Wire:loading for user feedback
   - Optimize re-renders (wire:key, wire:model.lazy)
   - Use AlpineJS for UI interactions (no server round-trips)

5. **Testing Standards**
   - Use Pest (not PHPUnit)
   - Minimum 80% code coverage
   - Feature tests for critical flows (sale processing, refunds, etc.)
   - Unit tests for services and calculations
   - Database transactions in tests
   - Mock external services (Stripe)

### File Organization

```
app/
├── Console/
│   └── Commands/           # Custom artisan commands
├── Events/                 # Domain events
├── Exceptions/             # Custom exceptions
├── Http/
│   ├── Controllers/
│   │   ├── Api/           # API controllers
│   │   └── POS/           # POS controllers
│   ├── Livewire/          # Livewire components
│   ├── Middleware/        # Custom middleware
│   └── Requests/          # Form request validation
├── Jobs/                  # Queue jobs
├── Listeners/             # Event listeners
├── Models/                # Eloquent models
├── Nova/
│   ├── Actions/           # Nova actions
│   ├── Cards/             # Nova custom cards
│   ├── Dashboards/        # Nova dashboards
│   ├── Filters/           # Nova filters
│   ├── Lenses/            # Nova lenses
│   ├── Metrics/           # Nova metrics
│   ├── Resources/         # Nova resources
│   └── Fields/            # Custom Nova fields
├── Observers/             # Model observers
├── Policies/              # Authorization policies
├── Providers/             # Service providers
├── Services/              # Business logic services
└── Traits/                # Reusable traits

database/
├── factories/             # Model factories
├── migrations/            # Database migrations
└── seeders/              # Database seeders

docs/                      # Project documentation
├── api/                  # API documentation
├── development/          # Developer guides
├── installation/         # Setup instructions
└── user-guide/           # User manuals

resources/
├── css/
│   └── app.css           # Tailwind CSS
├── js/
│   ├── app.js            # Main JS entry
│   └── components/       # Vue 3 components
└── views/
    ├── livewire/         # Livewire views
    └── receipts/         # Receipt templates

tests/
├── Feature/              # Feature tests
└── Unit/                 # Unit tests
```

### Naming Conventions

1. **Database**
   - Tables: plural, snake_case (users, product_variants, sale_items)
   - Columns: snake_case (user_id, created_at, product_name)
   - Foreign keys: singular_id (user_id, store_id, product_id)
   - Pivot tables: singular_singular (permission_role, not permissions_roles)

2. **Models**
   - Singular, PascalCase (User, Product, SaleItem)
   - Relationships: descriptive names (hasMany: items, belongsTo: category)

3. **Controllers**
   - Singular, PascalCase + Controller (ProductController, SaleController)
   - Methods: RESTful actions (index, show, store, update, destroy)

4. **Services**
   - Singular, PascalCase + Service (SaleService, InventoryService)
   - Methods: verb-based (processSale, updateStock, calculateTax)

5. **Livewire**
   - PascalCase (POSInterface, ShoppingCart, CustomerSelect)
   - Views: kebab-case (pos-interface, shopping-cart)

6. **Nova Resources**
   - Singular, PascalCase (Product, Sale, Customer)
   - Match model names

### Git Workflow

1. **Branch Strategy**
   - `main`: production-ready code
   - `develop`: integration branch
   - `feature/*`: new features
   - `fix/*`: bug fixes

2. **Commit Messages**
   - Format: `type(scope): description`
   - Types: feat, fix, docs, refactor, test, chore
   - Examples:
     - `feat(pos): add barcode scanning support`
     - `fix(inventory): correct stock calculation on refund`
     - `docs(api): update authentication endpoint docs`

3. **Pull Requests**
   - Descriptive title and summary
   - Link to related issues
   - Screenshots for UI changes
   - Test coverage report

## API Documentation

### Authentication
All API requests require Sanctum token authentication.

```
POST /api/auth/login
Body: { email, password }
Response: { token, user }
```

### Endpoints Overview
- **Products**: `/api/products` (list, search, details, variants)
- **Customers**: `/api/customers` (CRUD, search)
- **Sales**: `/api/sales` (create, retrieve, refund, history)
- **Cash Drawer**: `/api/cash-drawer` (open, close, status)
- **Reports**: `/api/reports/*` (sales, inventory, analytics)

Full API documentation: `docs/api/endpoints.md`

## Deployment

### Requirements
- **Server**: Linux (Ubuntu 22.04+)
- **PHP**: 8.3+
- **Database**: MySQL 8.0+
- **Cache/Queue**: Redis 6+
- **Web Server**: Nginx or Apache
- **SSL**: Required for production (Stripe requirement)

### Environment Setup
1. Clone repository
2. Copy `.env.example` to `.env`
3. Set database credentials
4. Set Redis connection
5. Set Stripe API keys
6. Set Nova license key
7. Run `composer install`
8. Run `npm install && npm run build`
9. Run `php artisan key:generate`
10. Run `php artisan migrate --seed`
11. Run `php artisan storage:link`
12. Configure queue worker (Supervisor)
13. Configure scheduled tasks (cron)

### Production Checklist
- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] HTTPS enabled
- [ ] Redis configured
- [ ] Queue worker running
- [ ] Scheduled tasks configured
- [ ] Backups automated
- [ ] Monitoring enabled
- [ ] Rate limiting configured
- [ ] CORS configured (if API public)

## Testing

### Running Tests
```bash
# All tests
php artisan test

# Specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# With coverage
php artisan test --coverage
```

### Test Coverage Requirements
- Overall: 80%+
- Services: 90%+
- Models: 70%+
- Controllers: 60%+

## Maintenance

### Daily Tasks
- Monitor queue jobs (failed jobs)
- Check error logs
- Review sales reports
- Verify backup completion

### Weekly Tasks
- Review slow query log
- Check disk space
- Update dependencies (security patches)
- Review user access logs

### Monthly Tasks
- Full system backup test
- Security audit
- Performance review
- User feedback review

## Future Enhancements

### Planned Features
- Multi-currency support
- Kitchen display system (restaurant mode)
- Table management (restaurant mode)
- Gift card system
- Employee time tracking and commission
- Advanced analytics with ML insights
- Mobile app (React Native)
- E-commerce sync (WooCommerce, Shopify)

### Integration Opportunities
- Accounting software (QuickBooks, Xero)
- Email marketing (Mailchimp)
- SMS notifications (Twilio)
- Loyalty program enhancements

## Support & Resources

### Documentation
- Installation: `docs/installation/setup.md`
- API Reference: `docs/api/endpoints.md`
- Cashier Manual: `docs/user-guide/cashier-manual.md`
- Admin Manual: `docs/user-guide/admin-manual.md`
- Architecture: `docs/development/architecture.md`

### External Resources
- Laravel 12: https://laravel.com/docs/12.x
- Nova 5: https://nova.laravel.com/docs/v5
- Livewire 3: https://livewire.laravel.com/docs
- Pest: https://pestphp.com
- Stripe: https://stripe.com/docs/api

### Key Contacts
- **Project Lead**: [Your Name]
- **Technical Lead**: [Tech Lead Name]
- **Repository**: [Git URL]

---

**Last Updated**: 2025-10-24
**Version**: 1.0.0
**Laravel**: 12.x
**Nova**: 5.7.6
**PHP**: 8.3+
