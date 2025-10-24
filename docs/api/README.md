# POS System API Documentation

## Overview

The POS System provides a RESTful API for mobile applications and third-party integrations. All API endpoints are authenticated using Laravel Sanctum token-based authentication.

**Base URL**: `https://your-domain.com/api`

**Authentication**: Bearer Token (Sanctum)

## Authentication

### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "token": "1|abcdefghijklmnopqrstuvwxyz",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "role": "Cashier",
    "store_id": 1
  }
}
```

### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

### Get Authenticated User
```http
GET /api/auth/user
Authorization: Bearer {token}
```

## Products

### List Products
```http
GET /api/products?page=1&per_page=50
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 50, max: 100)
- `category_id` (optional): Filter by category
- `active` (optional): Filter by active status (1 or 0)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Product Name",
      "sku": "PROD-001",
      "barcode": "1234567890",
      "category": {
        "id": 1,
        "name": "Category Name"
      },
      "variants": [
        {
          "id": 1,
          "name": "Default",
          "sku": "PROD-001-VAR-1",
          "price": 29.99,
          "stock": 100
        }
      ],
      "image": "https://your-domain.com/storage/products/image.jpg"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 50,
    "total": 500
  }
}
```

### Search Products
```http
GET /api/products/search?q=search_term
Authorization: Bearer {token}
```

**Query Parameters:**
- `q` (required): Search term (searches in name, SKU, barcode)

### Get Product Details
```http
GET /api/products/{id}
Authorization: Bearer {token}
```

## Customers

### List Customers
```http
GET /api/customers?page=1&per_page=50
Authorization: Bearer {token}
```

### Search Customers
```http
GET /api/customers/search?q=search_term
Authorization: Bearer {token}
```

### Get Customer Details
```http
GET /api/customers/{id}
Authorization: Bearer {token}
```

### Create Customer
```http
POST /api/customers
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "address": "123 Main St"
}
```

## Sales

### Create Sale
```http
POST /api/sales
Authorization: Bearer {token}
Content-Type: application/json

{
  "customer_id": 1,
  "items": [
    {
      "product_variant_id": 1,
      "quantity": 2,
      "unit_price": 29.99
    }
  ],
  "payments": [
    {
      "payment_method_id": 1,
      "amount": 59.98
    }
  ],
  "discount": 0,
  "notes": "Sale notes"
}
```

**Response:**
```json
{
  "data": {
    "id": 123,
    "reference": "SALE-2025-001",
    "customer": {
      "id": 1,
      "name": "John Doe"
    },
    "items": [...],
    "subtotal": 59.98,
    "tax": 5.40,
    "discount": 0,
    "total": 65.38,
    "status": "completed",
    "created_at": "2025-01-24T10:30:00Z"
  }
}
```

### Get Sale Details
```http
GET /api/sales/{id}
Authorization: Bearer {token}
```

### Process Refund
```http
POST /api/sales/{id}/refund
Authorization: Bearer {token}
Content-Type: application/json

{
  "items": [
    {
      "sale_item_id": 1,
      "quantity": 1
    }
  ],
  "reason": "Defective product"
}
```

### Sales History
```http
GET /api/sales/history?from=2025-01-01&to=2025-01-31
Authorization: Bearer {token}
```

## Cash Drawer

### Open Cash Drawer
```http
POST /api/cash-drawer/open
Authorization: Bearer {token}
Content-Type: application/json

{
  "opening_cash": 200.00
}
```

### Close Cash Drawer
```http
POST /api/cash-drawer/close
Authorization: Bearer {token}
Content-Type: application/json

{
  "actual_cash": 1450.00,
  "notes": "End of shift"
}
```

### Get Drawer Status
```http
GET /api/cash-drawer/status
Authorization: Bearer {token}
```

## Reports

### Today's Sales
```http
GET /api/reports/sales/today
Authorization: Bearer {token}
```

**Response:**
```json
{
  "total_sales": 1250.00,
  "transactions": 45,
  "average_transaction": 27.78,
  "payment_methods": {
    "cash": 500.00,
    "card": 750.00
  }
}
```

### Sales by Date Range
```http
GET /api/reports/sales/range?from=2025-01-01&to=2025-01-31
Authorization: Bearer {token}
```

### Low Stock Items
```http
GET /api/reports/inventory/low-stock
Authorization: Bearer {token}
```

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required."
    ]
  }
}
```

### 429 Too Many Requests
```json
{
  "message": "Too Many Attempts."
}
```

### 500 Server Error
```json
{
  "message": "Server Error",
  "error": "Error details"
}
```

## Rate Limiting

API requests are rate-limited to:
- **60 requests per minute** for authenticated users
- **10 requests per minute** for unauthenticated endpoints (login only)

Rate limit headers:
- `X-RateLimit-Limit`: Total requests allowed
- `X-RateLimit-Remaining`: Remaining requests
- `X-RateLimit-Reset`: Time when limit resets (Unix timestamp)

## Postman Collection

Import the Postman collection for easy API testing:
```
docs/api/POS-System-API.postman_collection.json
```

## Webhooks

Coming soon: Webhook support for real-time notifications.

## Support

For API support:
- Email: api-support@yourpos.com
- Documentation: https://your-domain.com/docs
