# Laravel POS System

A comprehensive, multi-tenant Point of Sale (POS) system built with Laravel 12, Nova 5.7, and Livewire 3.

## Features

### Core Functionality
- **Multi-Store Management**: Manage multiple stores with tenant isolation
- **Product Management**: Products with variants, categories, brands, and inventory tracking
- **Customer Management**: Customer profiles with loyalty points and store credit
- **Sales Processing**: Complete POS interface with cart, payment processing, and receipt printing
- **Inventory Management**: Stock tracking, low stock alerts, and stock movements
- **Purchase Orders**: Supplier management and purchase order processing
- **Reporting**: Comprehensive sales, inventory, and customer reports
- **User Management**: Role-based access control with multiple user types

### Technical Features
- **Laravel Nova Admin Panel**: Full-featured admin interface with custom dashboards, filters, actions, and lenses
- **Livewire POS Interface**: Real-time reactive POS interface without page reloads
- **REST API**: Complete API with Sanctum authentication for third-party integrations
- **Background Jobs**: Queue-based processing for reports and alerts
- **Caching Layer**: Redis-based caching for optimal performance
- **Multi-tenancy**: Store-level data isolation with middleware protection
- **PDF Generation**: Invoice generation with customizable templates
- **Comprehensive Testing**: Pest-based test suite covering all major features

## Requirements

- PHP 8.3 or higher
- MySQL 8.0 or higher / PostgreSQL 13 or higher
- Redis 6.0 or higher (for caching, sessions, and queues)
- Composer 2.x
- Node.js 20.x and npm/yarn (for asset compilation)

## Installation

### 1. Clone the repository
```bash
git clone <repository-url>
cd pos-system
```

### 2. Install dependencies
```bash
composer install
npm install
```

### 3. Environment configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your database and Redis credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_system
DB_USERNAME=root
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 4. Database setup
```bash
php artisan migrate
php artisan db:seed
```

### 5. Nova installation
```bash
# Add your Nova license key to .env
NOVA_LICENSE_KEY=your_license_key_here

# Publish Nova assets
php artisan nova:install
php artisan vendor:publish --provider="Laravel\Nova\NovaServiceProvider"
```

### 6. Storage setup
```bash
php artisan storage:link
```

### 7. Build assets
```bash
npm run build
# or for development
npm run dev
```

### 8. Start the application
```bash
# Start Laravel server
php artisan serve

# Start queue worker (in a separate terminal)
php artisan queue:work

# Start scheduler (in production, add to cron)
php artisan schedule:work
```

## Default Credentials

After seeding, you can login with:

- **Admin User**
  - Email: `admin@example.com`
  - Password: `password`
  - Access: Full system access (Admin Panel + POS + API)

- **Store Manager**
  - Email: `manager@example.com`
  - Password: `password`
  - Access: Store management + POS + Reports

- **Cashier**
  - Email: `cashier@example.com`
  - Password: `password`
  - Access: POS interface (limited admin access)

## Usage

### 1. Admin Panel (Back Office)

**Access URL:** `http://localhost:8000/admin`

**Login Steps:**
1. Navigate to `http://localhost:8000/admin/login`
2. Use credentials: `admin@example.com` / `password`
3. Explore the dashboard and resources

**Features:**
- 📊 3 Custom dashboards (Main, Inventory, Reports)
- 📦 30+ Nova resources (Products, Sales, Customers, etc.)
- 🔍 Advanced filters and search
- 📈 Real-time metrics (sales, customers, inventory)
- 💾 Bulk actions and exports
- 🎯 Custom lenses (Low Stock, Best Sellers)
- 📋 Comprehensive reports

### 2. POS Interface (Cashier)

**Access URL:** `http://localhost:8000/pos`

**Login Steps:**
1. **Must login to admin panel first:** `http://localhost:8000/admin/login`
2. Then navigate to: `http://localhost:8000/pos`
3. Start processing sales

**Features:**
- ⚡ Lightning-fast product search (< 200ms)
- 🔍 Barcode scanning support
- 🛒 Real-time shopping cart
- 💳 Multiple payment methods (cash, card, split payments)
- 👤 Customer selection and loyalty points
- 🎟️ Discount and coupon application
- 🧾 Receipt printing and email
- 💰 Cash drawer management
- ⌨️ Keyboard shortcuts (F1-F12 for speed)

**POS Routes:**
- `/pos` - Main POS interface
- `/pos/receipt/{sale}` - View/print receipt

### 3. REST API (Mobile Apps & Integrations)

**Base URL:** `http://localhost:8000/api`

**Authentication:** Laravel Sanctum (Token-based)

**Quick Start:**
```bash
# 1. Get API token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Response: {"user":{...},"token":"1|abcd..."}

# 2. Use token in requests
curl http://localhost:8000/api/products \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Key Endpoints:**
- `POST /api/login` - Get authentication token
- `GET /api/user` - Get authenticated user
- `GET /api/products` - List products (with search/filters)
- `GET /api/products/{id}` - Get product details
- `GET /api/customers` - List customers
- `POST /api/sales` - Create a sale
- `POST /api/sales/{id}/refund` - Process refund
- `GET /api/reports/sales` - Sales report
- `GET /api/reports/inventory` - Inventory report
- `GET /api/reports/customers` - Customer report
- `POST /api/logout` - Revoke token

**Rate Limits:**
- Login endpoint: 5 requests per minute
- API endpoints: 60 requests per minute

**Full API Documentation:** See `docs/API_DOCUMENTATION.md` for complete reference with request/response examples.

## Configuration

### POS Settings
Configure POS behavior in `.env`:

```env
POS_DEFAULT_TAX_RATE=0.00
POS_DEFAULT_CURRENCY=USD
POS_DEFAULT_CURRENCY_SYMBOL=$
POS_LOW_STOCK_THRESHOLD=10
POS_PREVENT_NEGATIVE_STOCK=true
POS_ENABLE_LOYALTY_POINTS=true
POS_LOYALTY_POINTS_RATIO=100
POS_REFUND_DAYS_LIMIT=30
```

### Queue Configuration
The system uses Redis queues for background processing:

```bash
# Start queue worker
php artisan queue:work --queue=default,reports,alerts

# Monitor failed jobs
php artisan queue:failed
```

### Scheduler Configuration
Add to crontab for scheduled tasks:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks:
- Daily sales reports (runs at 1:00 AM)
- Low stock alerts (runs at 9:00 AM)

## Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/SaleServiceTest.php

# Run with coverage
php artisan test --coverage
```

## Architecture

### Directory Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── API/          # API controllers
│   │   └── POS/          # POS controllers
│   └── Middleware/       # Custom middleware
├── Livewire/
│   └── POS/              # Livewire components
├── Models/               # Eloquent models
├── Nova/                 # Nova resources
│   ├── Actions/          # Custom Nova actions
│   ├── Dashboards/       # Custom dashboards
│   ├── Filters/          # Custom filters
│   ├── Lenses/           # Custom lenses
│   └── Metrics/          # Custom metrics
├── Observers/            # Model observers
├── Services/             # Business logic services
└── Jobs/                 # Queue jobs
```

### Key Services

**SaleService**: Handles sale creation, calculations, and processing
**InventoryService**: Manages stock levels and movements
**PaymentService**: Processes payments and validates amounts
**TaxService**: Calculates taxes based on rates
**DiscountService**: Applies discounts and promotions
**ReportService**: Generates various business reports
**CacheService**: Manages application caching

### Database Schema

Key tables:
- `stores` - Store information
- `users` - User accounts
- `products` - Product catalog
- `product_variants` - Product variants with pricing/stock
- `customers` - Customer information
- `sales` - Sales transactions
- `sale_items` - Individual sale line items
- `sale_payments` - Payment records
- `inventory_movements` - Stock movement history

## Customization

### Adding Custom Nova Actions
Create a new action:
```bash
php artisan nova:action ExportCustomers
```

### Adding Custom Metrics
Create a new metric:
```bash
php artisan nova:metric TodaysSales
```

### Adding Custom Filters
Create a new filter:
```bash
php artisan nova:filter DateRangeFilter
```

## Production Deployment

### Optimization
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### Security
- Set `APP_DEBUG=false` in production
- Use strong `APP_KEY`
- Configure proper file permissions
- Use HTTPS
- Set up proper CORS settings
- Implement rate limiting
- Regular security updates

### Performance
- Use Redis for caching, sessions, and queues
- Enable OPcache
- Use CDN for static assets
- Optimize database indexes
- Monitor with Laravel Telescope (dev) or third-party tools (prod)

## Troubleshooting

### Common Issues

**Nova not showing resources**
```bash
php artisan nova:publish
php artisan view:clear
```

**Queue jobs not processing**
```bash
php artisan queue:restart
php artisan queue:work
```

**Cache issues**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

**Permission errors**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new features
5. Ensure all tests pass
6. Submit a pull request

## License

This project is proprietary software. All rights reserved.

## Support

For support, email support@possystem.com or create an issue in the repository.

## Changelog

### Version 1.0.0 (Initial Release)
- Multi-store management
- Product catalog with variants
- Customer management with loyalty
- Complete POS interface
- Nova admin panel
- REST API
- Background jobs
- Comprehensive reporting
- PDF invoice generation

## Acknowledgments

- Laravel Framework
- Laravel Nova
- Livewire
- DomPDF
- Pest PHP

---

Built with ❤️ using Laravel
