# Laravel Point of Sale (POS) System

A comprehensive, multi-tenant Point of Sale system built with Laravel 12, Laravel Nova 5, and Livewire 3.

## Features

### 🏪 Multi-Tenant Architecture
- Store-based data isolation
- Centralized product catalog with store-specific inventory
- Per-store configuration and settings

### 🛒 Core POS Functionality
- Real-time product search with barcode scanning
- Shopping cart management
- Multiple payment methods support
- Customer management with loyalty points
- Receipt generation and printing
- Cash drawer management
- Sales returns and refunds

### 📦 Inventory Management
- Product catalog with variants
- Stock tracking and adjustments
- Purchase order management
- Low stock alerts
- Stock movement history
- Supplier management

### 💰 Discounts & Promotions
- Percentage and fixed amount discounts
- Coupon codes with usage limits
- Customer group-based pricing
- Time-based promotions

### 📊 Reporting & Analytics
- Sales reports (daily, weekly, monthly)
- Top-selling products
- Inventory valuation
- Customer purchase history
- Tax reports

### 🎨 Admin Panel (Laravel Nova)
- Intuitive admin interface
- Resource management
- Advanced filtering and searching
- Bulk operations
- Data export capabilities

### 🔌 REST API
- Sanctum authentication
- Product management endpoints
- Customer management endpoints
- Sales processing endpoints
- Comprehensive API documentation

## Tech Stack

- **Framework**: Laravel 12.x
- **Admin Panel**: Laravel Nova 5.7.6 (Inertia.js + Vue 3)
- **Frontend**: Livewire 3.x
- **Database**: MySQL 8.0+
- **Cache/Queue**: Redis
- **Payments**: Stripe Integration
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission
- **PDF Generation**: DomPDF
- **Excel Export**: Maatwebsite Excel

## Requirements

- PHP 8.3+
- MySQL 8.0+
- Redis 6.0+
- Composer
- Node.js & NPM

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd pos-system
```

### 2. Install Dependencies

```bash
composer install
npm install && npm run build
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_system
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
```

### 4. Database Setup

```bash
php artisan migrate:fresh --seed
```

This will create all tables and seed demo data including:
- 3 stores
- 10 users across different roles
- Sample products and inventory
- Customer groups and customers
- Payment methods and tax rates

### 5. Configure Laravel Nova

Nova is already installed. Access the admin panel at `/nova`.

### 6. Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000`

## Default Credentials

### Super Admin
- Email: admin@posstore.com
- Password: password

### Store Manager (Main Store)
- Email: manager@posstore.com
- Password: password

### Cashier (Main Store)
- Email: cashier1@posstore.com
- Password: password

## User Roles & Permissions

### Super Admin
- Full system access
- Manage all stores
- System-wide configuration

### Store Manager
- Manage store users
- Product and inventory management
- View all reports
- Cash drawer oversight

### Cashier
- Process sales
- Handle returns
- Manage customers
- Open/close cash drawer

### Inventory Manager
- Product management
- Stock adjustments
- Purchase orders
- Supplier management

### Accountant
- View reports
- Financial oversight
- No sale processing

## Key Directories

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── API/          # API controllers
│   │   │   └── POS/          # POS controllers
│   │   └── Resources/        # API resources
│   ├── Livewire/
│   │   └── POS/              # POS Livewire components
│   ├── Models/               # Eloquent models
│   ├── Nova/                 # Nova resources
│   ├── Observers/            # Model observers
│   └── Services/             # Business logic services
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── docs/                     # Documentation
└── resources/
    └── views/
        ├── livewire/         # Livewire views
        └── pos/              # POS views
```

## API Documentation

### Authentication

All API requests require authentication using Laravel Sanctum. Include the token in the Authorization header:

```
Authorization: Bearer {token}
```

### Endpoints

#### Products
- `GET /api/v1/products` - List all products
- `GET /api/v1/products/{id}` - Get product details
- `POST /api/v1/products` - Create product
- `PUT /api/v1/products/{id}` - Update product
- `DELETE /api/v1/products/{id}` - Delete product

#### Customers
- `GET /api/v1/customers` - List all customers
- `GET /api/v1/customers/{id}` - Get customer details
- `POST /api/v1/customers` - Create customer
- `PUT /api/v1/customers/{id}` - Update customer
- `DELETE /api/v1/customers/{id}` - Delete customer

#### Sales
- `GET /api/v1/sales` - List all sales
- `GET /api/v1/sales/{id}` - Get sale details
- `POST /api/v1/sales` - Create sale

#### Reports
- `GET /api/v1/reports/sales` - Sales report
- `GET /api/v1/reports/inventory` - Inventory report

## Configuration

### POS Settings (`config/pos.php`)

```php
'sale_reference_prefix' => 'SALE',
'default_tax_rate' => 10.00,
'currency' => 'USD',
'enable_loyalty_points' => true,
'loyalty_points_rate' => 0.1,
'allow_negative_stock' => false,
'low_stock_threshold' => 10,
```

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

```bash
./vendor/bin/pint
```

### Queue Workers

```bash
php artisan queue:work
```

## Deployment

### Production Checklist

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `php artisan view:cache`
6. Set up queue workers
7. Configure Redis for cache and sessions
8. Set up SSL certificate
9. Configure proper file permissions
10. Set up regular database backups

### Supervisor Configuration

```ini
[program:pos-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
```

## Features Roadmap

- [ ] Multi-currency support
- [ ] Advanced reporting dashboard
- [ ] Email notifications
- [ ] SMS notifications
- [ ] Inventory forecasting
- [ ] Employee time tracking
- [ ] Advanced barcode generation
- [ ] Customer loyalty tiers
- [ ] Gift card management
- [ ] Online ordering integration

## Support

For support, email support@posystem.com or open an issue in the repository.

## License

This project is proprietary software. All rights reserved.

## Credits

Built with:
- [Laravel](https://laravel.com)
- [Laravel Nova](https://nova.laravel.com)
- [Livewire](https://livewire.laravel.com)
- [Tailwind CSS](https://tailwindcss.com)

---

**Version**: 1.0.0
**Last Updated**: October 2025
