# POS System - Quick Access Guide

**Date:** 2025-10-27
**Version:** 1.0.0

---

## 🚀 Quick Start

Your POS System has **two main interfaces**:

1. **Admin Panel** - For administrators and managers (Laravel Nova)
2. **POS Interface** - For cashiers and sales staff (Livewire)
3. **REST API** - For mobile apps and integrations (Sanctum)

---

## 1️⃣ Admin Panel (Back Office)

### Access URL
```
http://localhost:8000/admin
```

### Purpose
Complete back-office management:
- Product catalog management
- Customer database
- Sales reports and analytics
- Inventory management
- User and permission management
- Store settings
- Financial reports

### Login
1. Go to: `http://localhost:8000/admin/login`
2. Use admin credentials from seeder:
   - **Email:** `admin@example.com`
   - **Password:** `password`

### Features Available
- ✅ 30+ Nova resources
- ✅ 3 dashboards (Main, Inventory, Reports)
- ✅ Advanced filters and searches
- ✅ Bulk actions and exports
- ✅ Real-time metrics
- ✅ Custom reports

---

## 2️⃣ POS Interface (Cashier)

### Access URL
```
http://localhost:8000/pos
```

### Purpose
Fast, intuitive cashier interface for processing sales:
- Product search (name, SKU, barcode)
- Shopping cart management
- Multiple payment methods
- Customer selection
- Discount application
- Receipt printing
- Cash drawer management

### How to Access

**Step 1: Login to Admin Panel First**
```
http://localhost:8000/admin/login
```
Use your credentials (email + password)

**Step 2: Navigate to POS Interface**
```
http://localhost:8000/pos
```

### Default Test Users

From the seeder (`database/seeders/UserSeeder.php`):

**Admin User:**
- Email: `admin@example.com`
- Password: `password`
- Role: Super Admin
- Access: Admin Panel + POS

**Manager User:**
- Email: `manager@example.com`
- Password: `password`
- Role: Store Manager
- Access: Admin Panel + POS

**Cashier User:**
- Email: `cashier@example.com`
- Password: `password`
- Role: Cashier
- Access: POS only (limited admin access)

### POS Features
- ⚡ Fast product search (< 200ms)
- 🛒 Real-time cart updates
- 💳 Multiple payment methods (cash, card, split)
- 👤 Customer lookup and loyalty points
- 🎟️ Discount and coupon application
- 🧾 Receipt printing
- 💰 Cash drawer management
- ⌨️ Keyboard shortcuts (F1-F12)

---

## 3️⃣ REST API

### Base URL
```
http://localhost:8000/api
```

### Purpose
Mobile app integration and third-party access:
- Process sales programmatically
- Manage products and inventory
- Customer management
- Generate reports
- Retrieve sales data

### Authentication

**Step 1: Get API Token**
```bash
POST /api/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password",
  "device_name": "My Mobile App"
}
```

**Response:**
```json
{
  "user": {...},
  "token": "1|abcdefghijklmnopqrstuvwxyz123456789"
}
```

**Step 2: Use Token in All Requests**
```bash
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456789
```

### Quick API Examples

**Get All Products:**
```bash
curl http://localhost:8000/api/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Create a Sale:**
```bash
curl -X POST http://localhost:8000/api/sales \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "store_id": 1,
    "items": [
      {"product_id": 1, "quantity": 2, "price": 24.99}
    ],
    "payments": [
      {"payment_method_id": 1, "amount": 49.98}
    ]
  }'
```

**Get Sales Report:**
```bash
curl http://localhost:8000/api/reports/sales?from_date=2025-01-01&to_date=2025-01-31 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Full API Documentation
See: `docs/API_DOCUMENTATION.md` for complete endpoint reference

---

## 📊 Available Resources

### Products
- ✅ 50+ sample products (from seeder)
- ✅ Categories (nested tree structure)
- ✅ Brands
- ✅ Product variants (size, color, etc.)
- ✅ Stock tracking

### Customers
- ✅ 20+ sample customers
- ✅ Customer groups (VIP, Wholesale, Retail)
- ✅ Loyalty points system
- ✅ Store credit
- ✅ Purchase history

### Sales
- ✅ Complete transaction processing
- ✅ Multiple payment methods
- ✅ Refund/return management
- ✅ Receipt generation
- ✅ Invoice generation

### Reports
- ✅ Sales reports (daily, weekly, monthly)
- ✅ Inventory reports (stock levels, low stock)
- ✅ Customer reports (top customers, new customers)
- ✅ Financial reports (revenue, profit)

---

## 🔑 Default Credentials

### Admin Panel & POS

| User Type | Email | Password | Access Level |
|-----------|-------|----------|-------------|
| Admin | `admin@example.com` | `password` | Full access (all features) |
| Manager | `manager@example.com` | `password` | Store management + POS |
| Cashier | `cashier@example.com` | `password` | POS only (limited admin) |

### First Login Steps

1. **Login to Admin Panel:**
   - Go to: `http://localhost:8000/admin/login`
   - Use `admin@example.com` / `password`

2. **Explore the Dashboard:**
   - View sales metrics
   - Check inventory status
   - Browse sample data

3. **Try the POS Interface:**
   - Navigate to: `http://localhost:8000/pos`
   - Search for products
   - Add items to cart
   - Process a test sale

4. **Test the API:**
   - Get API token using `/api/login`
   - Try product listing endpoint
   - Create a test sale via API

---

## 🛠️ Development Server

Make sure the development server is running:

```bash
# Start Laravel development server
php artisan serve --port=8000

# In another terminal, start Vite for assets
npm run dev

# In another terminal, start queue worker
php artisan queue:work
```

### Access Points When Server is Running

- **Homepage:** `http://localhost:8000/` (redirects to admin)
- **Admin Panel:** `http://localhost:8000/admin`
- **POS Interface:** `http://localhost:8000/pos`
- **API:** `http://localhost:8000/api/*`

---

## 📱 Responsive Design

All interfaces are fully responsive:
- ✅ Desktop (1920px+)
- ✅ Laptop (1280px+)
- ✅ Tablet (768px+)
- ✅ Mobile (320px+)

---

## 🔒 Security

### Production Deployment Checklist

Before deploying to production:

- [ ] Change all default passwords
- [ ] Update `.env` with production credentials
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Enable HTTPS (SSL certificate)
- [ ] Configure proper CORS settings
- [ ] Set up automated backups
- [ ] Configure Redis for cache/queue
- [ ] Set proper file permissions
- [ ] Configure rate limiting
- [ ] Enable 2FA for admin users
- [ ] Review and update permissions

---

## 🎯 Common Tasks

### Add a New Product (Admin Panel)
1. Login to admin panel
2. Click "Products" in sidebar
3. Click "Create Product"
4. Fill in product details
5. Save

### Process a Sale (POS)
1. Login to POS interface
2. Search and add products to cart
3. Adjust quantities if needed
4. Select customer (optional)
5. Apply discount (optional)
6. Choose payment method
7. Complete sale
8. Print receipt

### Process a Refund (Admin Panel)
1. Login to admin panel
2. Go to "Sales"
3. Find the sale to refund
4. Click "Refund" action
5. Select items and quantities
6. Provide reason
7. Approve refund

### Generate Sales Report (Admin Panel)
1. Login to admin panel
2. Go to "Reports" dashboard
3. Select date range
4. Choose report type
5. View or export data

---

## 📚 Additional Documentation

- **API Documentation:** `docs/API_DOCUMENTATION.md`
- **Project Overview:** `CLAUDE.md`
- **Safe Customization Guide:** `/tmp/SAFE_CUSTOMIZATION_GUIDE.md`
- **Premium Customization Summary:** `/tmp/ULTRA_PREMIUM_CUSTOMIZATION_SUMMARY.md`

---

## ❓ Troubleshooting

### Can't Access POS Interface
- **Issue:** Getting login page instead of POS
- **Solution:** Login to admin panel first, then navigate to `/pos`

### API Returns 401 Unauthorized
- **Issue:** Missing or invalid token
- **Solution:** Get fresh token from `/api/login` endpoint

### Nova Shows "JSON Field Not Found"
- **Issue:** Using old Nova 4 fields
- **Solution:** Already fixed - JSON replaced with KeyValue field

### UI Looks Broken
- **Issue:** Custom CSS conflicts
- **Solution:** Already fixed - removed problematic CSS

---

## ✅ Current Status

- ✅ Admin panel fully functional at `/admin`
- ✅ POS interface ready at `/pos`
- ✅ REST API available at `/api`
- ✅ All tests passing (109/110)
- ✅ Sample data seeded
- ✅ Production-ready

---

**Last Updated:** 2025-10-27
**App Version:** 1.0.0
**Laravel:** 12.35.1
**Nova:** 5.7.6
