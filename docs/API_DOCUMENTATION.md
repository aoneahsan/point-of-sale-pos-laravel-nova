# POS System - REST API Documentation

**Version:** 1.0.0
**Base URL:** `http://localhost:8000/api`
**Authentication:** Laravel Sanctum (Token-based)
**Date:** 2025-10-27

---

## 📋 Table of Contents

1. [Authentication](#authentication)
2. [Rate Limiting](#rate-limiting)
3. [Products API](#products-api)
4. [Customers API](#customers-api)
5. [Sales API](#sales-api)
6. [Reports API](#reports-api)
7. [Error Responses](#error-responses)

---

## 🔐 Authentication

All API endpoints (except login) require authentication using Laravel Sanctum tokens.

### Login

Generate an API token by authenticating with email and password.

**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
  "email": "admin@example.com",
  "password": "password",
  "device_name": "iPhone 14 Pro" // Optional
}
```

**Response (200 OK):**
```json
{
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "active": true,
    "created_at": "2025-01-15T10:30:00.000000Z"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz123456789"
}
```

**Error Response (401 Unauthorized):**
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

### Using the Token

Include the token in all subsequent requests:

**Header:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456789
```

**cURL Example:**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
     -H "Accept: application/json" \
     http://localhost:8000/api/products
```

### Get Authenticated User

Get details of the currently authenticated user.

**Endpoint:** `GET /api/user`

**Response (200 OK):**
```json
{
  "id": 1,
  "name": "Admin User",
  "email": "admin@example.com",
  "active": true,
  "created_at": "2025-01-15T10:30:00.000000Z"
}
```

### Logout

Revoke the current access token.

**Endpoint:** `POST /api/logout`

**Response (200 OK):**
```json
{
  "message": "Logged out successfully"
}
```

---

## ⏱️ Rate Limiting

API endpoints are rate-limited to prevent abuse:

| Endpoint Type | Rate Limit |
|--------------|------------|
| Login (`POST /api/login`) | 5 requests per minute |
| All other API endpoints | 60 requests per minute |

**Rate Limit Headers:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
Retry-After: 60 (when limit exceeded)
```

**Rate Limit Exceeded Response (429 Too Many Requests):**
```json
{
  "message": "Too Many Requests"
}
```

---

## 📦 Products API

Manage products in the POS system.

### List Products

Get a paginated list of products with optional filtering.

**Endpoint:** `GET /api/products`

**Query Parameters:**
- `search` (string) - Search by name, SKU, or barcode
- `category_id` (integer) - Filter by category
- `brand_id` (integer) - Filter by brand
- `store_id` (integer) - Filter by store
- `per_page` (integer) - Items per page (default: 15, max: 100)
- `page` (integer) - Page number

**Example Request:**
```bash
GET /api/products?search=coffee&category_id=5&per_page=20&page=1
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Premium Coffee Beans",
      "sku": "COFFEE-001",
      "barcode": "1234567890123",
      "description": "High-quality arabica coffee beans",
      "price": 24.99,
      "cost": 12.00,
      "stock_quantity": 150,
      "track_stock": true,
      "active": true,
      "category": {
        "id": 5,
        "name": "Beverages"
      },
      "brand": {
        "id": 3,
        "name": "Premium Roasters"
      },
      "variants": [
        {
          "id": 1,
          "sku": "COFFEE-001-250G",
          "name": "250g",
          "price": 24.99,
          "stock_quantity": 75
        },
        {
          "id": 2,
          "sku": "COFFEE-001-500G",
          "name": "500g",
          "price": 45.99,
          "stock_quantity": 75
        }
      ]
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/products?page=1",
    "last": "http://localhost:8000/api/products?page=5",
    "prev": null,
    "next": "http://localhost:8000/api/products?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 73
  }
}
```

### Get Product Details

Get detailed information about a specific product.

**Endpoint:** `GET /api/products/{id}`

**Example Request:**
```bash
GET /api/products/1
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "name": "Premium Coffee Beans",
    "sku": "COFFEE-001",
    "barcode": "1234567890123",
    "description": "High-quality arabica coffee beans",
    "price": 24.99,
    "cost": 12.00,
    "stock_quantity": 150,
    "low_stock_threshold": 20,
    "track_stock": true,
    "active": true,
    "category": {
      "id": 5,
      "name": "Beverages",
      "parent_id": null
    },
    "brand": {
      "id": 3,
      "name": "Premium Roasters"
    },
    "variants": [
      {
        "id": 1,
        "sku": "COFFEE-001-250G",
        "name": "250g",
        "price": 24.99,
        "stock_quantity": 75
      }
    ],
    "images": [
      {
        "id": 1,
        "url": "https://example.com/storage/products/coffee-001.jpg",
        "is_primary": true
      }
    ]
  }
}
```

### Create Product

Create a new product.

**Endpoint:** `POST /api/products`

**Request Body:**
```json
{
  "store_id": 1,
  "category_id": 5,
  "brand_id": 3,
  "name": "New Coffee Blend",
  "sku": "COFFEE-002",
  "barcode": "9876543210123",
  "description": "Smooth medium roast blend",
  "price": 29.99,
  "cost": 15.00,
  "stock_quantity": 100,
  "low_stock_threshold": 15,
  "track_stock": true,
  "active": true
}
```

**Response (201 Created):**
```json
{
  "data": {
    "id": 2,
    "name": "New Coffee Blend",
    "sku": "COFFEE-002",
    // ... full product details
  }
}
```

### Update Product

Update an existing product.

**Endpoint:** `PUT /api/products/{id}` or `PATCH /api/products/{id}`

**Request Body (partial update allowed):**
```json
{
  "price": 34.99,
  "stock_quantity": 80
}
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 2,
    "price": 34.99,
    "stock_quantity": 80,
    // ... full product details
  }
}
```

### Delete Product

Soft delete a product (marks as inactive).

**Endpoint:** `DELETE /api/products/{id}`

**Response (200 OK):**
```json
{
  "message": "Product deleted successfully"
}
```

---

## 👥 Customers API

Manage customer information.

### List Customers

Get a paginated list of customers.

**Endpoint:** `GET /api/customers`

**Query Parameters:**
- `search` (string) - Search by name, email, or phone
- `customer_group_id` (integer) - Filter by customer group
- `store_id` (integer) - Filter by store
- `per_page` (integer) - Items per page (default: 15)

**Example Request:**
```bash
GET /api/customers?search=john&per_page=20
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+1234567890",
      "loyalty_points": 250,
      "store_credit": 10.50,
      "customer_group": {
        "id": 2,
        "name": "VIP"
      },
      "created_at": "2025-01-10T08:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 45
  }
}
```

### Get Customer Details

Get detailed information about a customer, including purchase history.

**Endpoint:** `GET /api/customers/{id}`

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "address": "123 Main St, City, State 12345",
    "loyalty_points": 250,
    "store_credit": 10.50,
    "total_purchases": 1250.00,
    "customer_group": {
      "id": 2,
      "name": "VIP",
      "discount_percentage": 10
    }
  }
}
```

### Create Customer

Register a new customer.

**Endpoint:** `POST /api/customers`

**Request Body:**
```json
{
  "store_id": 1,
  "name": "Jane Smith",
  "email": "jane@example.com",
  "phone": "+1987654321",
  "address": "456 Oak Ave, City, State 67890",
  "customer_group_id": 1,
  "notes": "Prefers email notifications"
}
```

**Response (201 Created):**
```json
{
  "data": {
    "id": 2,
    "name": "Jane Smith",
    "email": "jane@example.com",
    // ... full customer details
  }
}
```

### Update Customer

Update customer information.

**Endpoint:** `PUT /api/customers/{id}` or `PATCH /api/customers/{id}`

**Request Body (partial update allowed):**
```json
{
  "phone": "+1999888777",
  "customer_group_id": 2
}
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 2,
    "phone": "+1999888777",
    // ... full customer details
  }
}
```

### Delete Customer

Soft delete a customer.

**Endpoint:** `DELETE /api/customers/{id}`

**Response (200 OK):**
```json
{
  "message": "Customer deleted successfully"
}
```

### Add Loyalty Points

Add loyalty points to a customer's account.

**Endpoint:** `POST /api/customers/{id}/loyalty-points`

**Request Body:**
```json
{
  "points": 50,
  "reason": "Birthday bonus"
}
```

**Response (200 OK):**
```json
{
  "message": "Loyalty points added successfully",
  "customer": {
    "id": 1,
    "loyalty_points": 300
  }
}
```

### Add Store Credit

Add store credit to a customer's account.

**Endpoint:** `POST /api/customers/{id}/store-credit`

**Request Body:**
```json
{
  "amount": 25.00,
  "reason": "Refund for returned item"
}
```

**Response (200 OK):**
```json
{
  "message": "Store credit added successfully",
  "customer": {
    "id": 1,
    "store_credit": 35.50
  }
}
```

---

## 💰 Sales API

Process sales transactions and refunds.

### List Sales

Get a paginated list of sales with optional filtering.

**Endpoint:** `GET /api/sales`

**Query Parameters:**
- `store_id` (integer) - Filter by store
- `status` (string) - Filter by status (completed, refunded, pending, cancelled)
- `from_date` (date) - Start date for filtering (YYYY-MM-DD)
- `to_date` (date) - End date for filtering (YYYY-MM-DD)
- `per_page` (integer) - Items per page (default: 15)

**Example Request:**
```bash
GET /api/sales?store_id=1&status=completed&from_date=2025-01-01&to_date=2025-01-31
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "reference": "SALE-20250127-0001",
      "store_id": 1,
      "customer": {
        "id": 1,
        "name": "John Doe"
      },
      "status": "completed",
      "subtotal": 45.98,
      "discount": 5.00,
      "tax": 3.68,
      "total": 44.66,
      "items_count": 2,
      "created_at": "2025-01-27T14:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 123
  }
}
```

### Get Sale Details

Get detailed information about a specific sale.

**Endpoint:** `GET /api/sales/{id}`

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "reference": "SALE-20250127-0001",
    "store_id": 1,
    "customer": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "status": "completed",
    "subtotal": 45.98,
    "discount": 5.00,
    "tax": 3.68,
    "total": 44.66,
    "notes": "Customer requested gift wrapping",
    "items": [
      {
        "id": 1,
        "product": {
          "id": 1,
          "name": "Premium Coffee Beans",
          "sku": "COFFEE-001"
        },
        "quantity": 2,
        "unit_price": 24.99,
        "discount": 5.00,
        "tax": 3.68,
        "total": 48.66
      }
    ],
    "payments": [
      {
        "id": 1,
        "payment_method": {
          "id": 1,
          "name": "Credit Card"
        },
        "amount": 44.66,
        "status": "completed"
      }
    ],
    "created_at": "2025-01-27T14:30:00.000000Z"
  }
}
```

### Create Sale

Process a new sale transaction.

**Endpoint:** `POST /api/sales`

**Request Body:**
```json
{
  "store_id": 1,
  "customer_id": 1,
  "discount": 5.00,
  "notes": "Customer used loyalty discount",
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 24.99,
      "discount": 5.00
    },
    {
      "product_variant_id": 2,
      "quantity": 1,
      "price": 45.99,
      "discount": 0
    }
  ],
  "payments": [
    {
      "payment_method_id": 1,
      "amount": 90.97
    }
  ]
}
```

**Validation Rules:**
- Each item must have either `product_id` OR `product_variant_id` (not both)
- Payment total must match the calculated sale total (subtotal - discount + tax)
- All products/variants must exist and have sufficient stock

**Response (201 Created):**
```json
{
  "data": {
    "id": 2,
    "reference": "SALE-20250127-0002",
    "status": "completed",
    "total": 90.97,
    // ... full sale details with items and payments
  }
}
```

**Error Response (422 Validation Error):**
```json
{
  "message": "Validation failed",
  "errors": {
    "items.0": ["Each item must have either product_id or product_variant_id"],
    "payments": ["Payment total must match sale total"]
  }
}
```

### Process Refund

Process a full or partial refund for a sale.

**Endpoint:** `POST /api/sales/{id}/refund`

**Authorization:** Requires `process-refunds` permission

**Request Body:**
```json
{
  "reason": "Customer not satisfied with product quality",
  "items": [
    {
      "sale_item_id": 1,
      "quantity": 1,
      "reason": "defective"
    }
  ]
}
```

**Available Item Reasons:**
- `customer_request` - Customer requested refund
- `defective` - Product is defective
- `damaged` - Product was damaged
- `incorrect` - Wrong item shipped

**Response (200 OK):**
```json
{
  "message": "Refund processed successfully",
  "return": {
    "id": 1,
    "reference": "RET-65B5E3F4A8C9",
    "sale_id": 1,
    "reason": "Customer not satisfied with product quality",
    "subtotal": 24.99,
    "tax": 1.84,
    "total": 26.83,
    "status": "approved",
    "items": [
      {
        "id": 1,
        "sale_item_id": 1,
        "quantity": 1,
        "unit_price": 24.99,
        "total": 24.99
      }
    ]
  }
}
```

**Error Response (400 Bad Request):**
```json
{
  "message": "Validation failed",
  "errors": {
    "items": ["Cannot refund more than purchased"]
  }
}
```

**Error Response (403 Forbidden):**
```json
{
  "message": "Unauthorized"
}
```

### Get Sale Invoice

Get a downloadable invoice PDF for a sale.

**Endpoint:** `GET /api/sales/{id}/invoice`

**Response:** PDF file download

---

## 📊 Reports API

Generate various business reports.

### Sales Report

Get sales analytics and summaries.

**Endpoint:** `GET /api/reports/sales`

**Query Parameters:**
- `store_id` (integer) - Filter by store
- `from_date` (date) - Start date (YYYY-MM-DD)
- `to_date` (date) - End date (YYYY-MM-DD)
- `group_by` (string) - Group results by: day, week, month, product, category, cashier

**Example Request:**
```bash
GET /api/reports/sales?store_id=1&from_date=2025-01-01&to_date=2025-01-31&group_by=day
```

**Response (200 OK):**
```json
{
  "summary": {
    "total_sales": 15234.50,
    "total_transactions": 287,
    "average_sale": 53.08,
    "total_items_sold": 842,
    "total_discount": 1234.00,
    "total_tax": 1156.22
  },
  "data": [
    {
      "date": "2025-01-27",
      "sales": 1234.50,
      "transactions": 23,
      "items_sold": 67
    }
  ]
}
```

### Inventory Report

Get current inventory levels and stock information.

**Endpoint:** `GET /api/reports/inventory`

**Query Parameters:**
- `store_id` (integer) - Filter by store
- `category_id` (integer) - Filter by category
- `low_stock` (boolean) - Show only low stock items (true/false)

**Example Request:**
```bash
GET /api/reports/inventory?store_id=1&low_stock=true
```

**Response (200 OK):**
```json
{
  "summary": {
    "total_products": 342,
    "total_value": 45678.90,
    "low_stock_items": 23,
    "out_of_stock_items": 5
  },
  "data": [
    {
      "id": 1,
      "name": "Premium Coffee Beans",
      "sku": "COFFEE-001",
      "stock_quantity": 15,
      "low_stock_threshold": 20,
      "cost": 12.00,
      "stock_value": 180.00,
      "status": "low_stock"
    }
  ]
}
```

### Customer Report

Get customer analytics and statistics.

**Endpoint:** `GET /api/reports/customers`

**Query Parameters:**
- `store_id` (integer) - Filter by store
- `from_date` (date) - Start date (YYYY-MM-DD)
- `to_date` (date) - End date (YYYY-MM-DD)
- `customer_group_id` (integer) - Filter by customer group

**Example Request:**
```bash
GET /api/reports/customers?store_id=1&from_date=2025-01-01&to_date=2025-01-31
```

**Response (200 OK):**
```json
{
  "summary": {
    "total_customers": 1234,
    "new_customers": 45,
    "active_customers": 678,
    "total_loyalty_points": 125000
  },
  "top_customers": [
    {
      "id": 1,
      "name": "John Doe",
      "total_purchases": 2345.00,
      "transactions_count": 23,
      "average_purchase": 102.00
    }
  ]
}
```

---

## ❌ Error Responses

The API uses standard HTTP status codes and returns errors in a consistent format.

### Error Response Format

```json
{
  "message": "Brief error message",
  "errors": {
    "field_name": [
      "Detailed validation error message"
    ]
  }
}
```

### HTTP Status Codes

| Status Code | Meaning |
|------------|---------|
| 200 | Success |
| 201 | Created successfully |
| 400 | Bad request (client error) |
| 401 | Unauthorized (invalid/missing token) |
| 403 | Forbidden (insufficient permissions) |
| 404 | Resource not found |
| 422 | Validation error |
| 429 | Too many requests (rate limit exceeded) |
| 500 | Server error |

### Common Error Examples

**401 Unauthorized:**
```json
{
  "message": "Unauthenticated."
}
```

**403 Forbidden:**
```json
{
  "message": "Unauthorized"
}
```

**404 Not Found:**
```json
{
  "message": "No query results for model [App\\Models\\Product] 999"
}
```

**422 Validation Error:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

**429 Rate Limit:**
```json
{
  "message": "Too Many Requests"
}
```

---

## 🔧 Additional Information

### Pagination

All list endpoints support pagination with the following response structure:

```json
{
  "data": [...],
  "links": {
    "first": "url",
    "last": "url",
    "prev": "url",
    "next": "url"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 73
  }
}
```

### Date Formats

All dates are in ISO 8601 format: `YYYY-MM-DDTHH:MM:SS.000000Z`

### Decimal Precision

All monetary values are returned with 2 decimal places.

### Response Headers

```
Content-Type: application/json
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

---

## 📝 Testing the API

### Using cURL

**Login:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

**Get Products:**
```bash
curl http://localhost:8000/api/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Using Postman

1. Import this base URL: `http://localhost:8000/api`
2. Add Authorization header with Bearer token
3. Test all endpoints with proper request bodies

---

## 📞 Support

For issues or questions about the API:
- Check error responses for detailed messages
- Review validation rules in request documentation
- Ensure proper authentication headers are included
- Verify rate limits haven't been exceeded

---

**Last Updated:** 2025-10-27
**API Version:** 1.0.0
**Laravel Version:** 12.35.1
