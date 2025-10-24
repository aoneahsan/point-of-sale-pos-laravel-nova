# POS System API Documentation

## Authentication

The API uses Laravel Sanctum for authentication. All protected endpoints require a Bearer token.

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password",
  "device_name": "mobile-app"
}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com"
  },
  "token": "1|abc123..."
}
```

### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Logged out successfully"
}
```

## Products

### List Products
```http
GET /api/products
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (int) - Page number
- `per_page` (int) - Items per page (default: 15)
- `search` (string) - Search by name, SKU, or barcode
- `category_id` (int) - Filter by category
- `active` (boolean) - Filter by active status

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Product Name",
      "sku": "PROD-001",
      "barcode": "123456789",
      "description": "Product description",
      "category": {
        "id": 1,
        "name": "Category Name"
      },
      "variants": [
        {
          "id": 1,
          "name": "Variant Name",
          "price": 19.99,
          "stock": 100
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 50,
    "per_page": 15
  }
}
```

### Get Product
```http
GET /api/products/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
  "id": 1,
  "name": "Product Name",
  "sku": "PROD-001",
  "barcode": "123456789",
  "description": "Product description",
  "active": true,
  "category": {
    "id": 1,
    "name": "Category Name"
  },
  "brand": {
    "id": 1,
    "name": "Brand Name"
  },
  "variants": [
    {
      "id": 1,
      "name": "Variant Name",
      "sku": "VAR-001",
      "price": 19.99,
      "cost": 10.00,
      "stock": 100,
      "low_stock_threshold": 5
    }
  ]
}
```

### Create Product
```http
POST /api/products
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "New Product",
  "sku": "PROD-002",
  "barcode": "987654321",
  "description": "Product description",
  "category_id": 1,
  "brand_id": 1,
  "tax_rate_id": 1,
  "unit": "piece",
  "active": true,
  "track_inventory": true
}
```

### Update Product
```http
PUT /api/products/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Product Name",
  "price": 29.99
}
```

### Delete Product
```http
DELETE /api/products/{id}
Authorization: Bearer {token}
```

### Get Product Variants
```http
GET /api/products/{id}/variants
Authorization: Bearer {token}
```

## Customers

### List Customers
```http
GET /api/customers
Authorization: Bearer {token}
```

**Query Parameters:**
- `search` (string) - Search by name, email, or phone
- `customer_group_id` (int) - Filter by customer group

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+1234567890",
      "loyalty_points": 150,
      "store_credit": 25.00,
      "customer_group": {
        "id": 1,
        "name": "VIP"
      }
    }
  ]
}
```

### Get Customer
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
  "name": "Jane Doe",
  "email": "jane@example.com",
  "phone": "+1234567890",
  "address": "123 Main St",
  "city": "New York",
  "country": "USA",
  "customer_group_id": 1
}
```

### Update Customer
```http
PUT /api/customers/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Jane Smith",
  "phone": "+0987654321"
}
```

### Add Loyalty Points
```http
POST /api/customers/{id}/loyalty-points
Authorization: Bearer {token}
Content-Type: application/json

{
  "points": 50,
  "reason": "Birthday bonus"
}
```

### Add Store Credit
```http
POST /api/customers/{id}/store-credit
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 25.00,
  "reason": "Refund"
}
```

## Sales

### List Sales
```http
GET /api/sales
Authorization: Bearer {token}
```

**Query Parameters:**
- `store_id` (int) - Filter by store
- `status` (string) - Filter by status (completed, pending, on_hold, cancelled, refunded)
- `start_date` (date) - Filter from date
- `end_date` (date) - Filter to date

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "reference": "SALE-001",
      "store": {
        "id": 1,
        "name": "Store 1"
      },
      "customer": {
        "id": 1,
        "name": "John Doe"
      },
      "subtotal": 100.00,
      "tax": 10.00,
      "discount": 5.00,
      "total": 105.00,
      "status": "completed",
      "created_at": "2024-01-01T10:00:00Z"
    }
  ]
}
```

### Get Sale
```http
GET /api/sales/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
  "id": 1,
  "reference": "SALE-001",
  "store": {...},
  "customer": {...},
  "items": [
    {
      "id": 1,
      "product_variant": {
        "id": 1,
        "name": "Product Variant",
        "sku": "VAR-001"
      },
      "quantity": 2,
      "price": 50.00,
      "tax": 5.00,
      "discount": 2.50,
      "subtotal": 100.00,
      "total": 102.50
    }
  ],
  "payments": [
    {
      "id": 1,
      "payment_method": {
        "id": 1,
        "name": "Cash"
      },
      "amount": 105.00,
      "reference": "CASH-001"
    }
  ],
  "subtotal": 100.00,
  "tax": 10.00,
  "discount": 5.00,
  "total": 105.00,
  "status": "completed",
  "notes": "Customer notes",
  "created_at": "2024-01-01T10:00:00Z"
}
```

### Create Sale
```http
POST /api/sales
Authorization: Bearer {token}
Content-Type: application/json

{
  "store_id": 1,
  "customer_id": 1,
  "items": [
    {
      "product_variant_id": 1,
      "quantity": 2,
      "price": 50.00,
      "discount": 2.50
    }
  ],
  "payments": [
    {
      "payment_method_id": 1,
      "amount": 105.00,
      "reference": "CASH-001"
    }
  ],
  "discount": 5.00,
  "notes": "Customer notes"
}
```

**Response:**
```json
{
  "id": 1,
  "reference": "SALE-001",
  "total": 105.00,
  "status": "completed",
  "message": "Sale created successfully"
}
```

### Refund Sale
```http
POST /api/sales/{id}/refund
Authorization: Bearer {token}
Content-Type: application/json

{
  "reason": "Defective product"
}
```

### Get Sale Invoice
```http
GET /api/sales/{id}/invoice
Authorization: Bearer {token}
```

Returns a PDF invoice for the sale.

## Reports

### Sales Report
```http
GET /api/reports/sales
Authorization: Bearer {token}
```

**Query Parameters:**
- `store_id` (int, required) - Store ID
- `start_date` (date, required) - Start date (YYYY-MM-DD)
- `end_date` (date, required) - End date (YYYY-MM-DD)
- `group_by` (string) - Group by day, week, month (default: day)

**Response:**
```json
{
  "period": {
    "start": "2024-01-01",
    "end": "2024-01-31"
  },
  "summary": {
    "total_sales": 50,
    "total_revenue": 5000.00,
    "total_profit": 2000.00,
    "average_sale": 100.00,
    "total_tax": 500.00,
    "total_discount": 250.00
  },
  "sales_by_period": [
    {
      "date": "2024-01-01",
      "sales_count": 10,
      "revenue": 1000.00
    }
  ],
  "top_products": [
    {
      "product": "Product Name",
      "quantity_sold": 50,
      "revenue": 1000.00
    }
  ],
  "payment_methods": [
    {
      "method": "Cash",
      "count": 30,
      "total": 3000.00
    }
  ]
}
```

### Inventory Report
```http
GET /api/reports/inventory
Authorization: Bearer {token}
```

**Query Parameters:**
- `store_id` (int, required) - Store ID

**Response:**
```json
{
  "summary": {
    "total_products": 100,
    "total_variants": 250,
    "total_stock_value": 50000.00,
    "low_stock_items": 15,
    "out_of_stock_items": 5
  },
  "low_stock_products": [
    {
      "product": "Product Name",
      "variant": "Variant Name",
      "current_stock": 3,
      "low_stock_threshold": 5,
      "stock_value": 150.00
    }
  ],
  "stock_by_category": [
    {
      "category": "Electronics",
      "product_count": 25,
      "total_stock": 500,
      "stock_value": 15000.00
    }
  ]
}
```

### Customer Report
```http
GET /api/reports/customers
Authorization: Bearer {token}
```

**Query Parameters:**
- `store_id` (int, required) - Store ID
- `start_date` (date, optional) - Start date
- `end_date` (date, optional) - End date

**Response:**
```json
{
  "summary": {
    "total_customers": 500,
    "new_customers": 50,
    "active_customers": 200,
    "total_loyalty_points": 50000,
    "total_store_credit": 5000.00
  },
  "top_customers": [
    {
      "customer": "John Doe",
      "total_purchases": 50,
      "total_spent": 5000.00,
      "loyalty_points": 500
    }
  ],
  "customer_groups": [
    {
      "group": "VIP",
      "customer_count": 50,
      "total_spent": 25000.00
    }
  ]
}
```

## Error Responses

All endpoints may return the following error responses:

### 400 Bad Request
```json
{
  "message": "Validation error",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
  "message": "Unauthorized access to this store"
}
```

### 404 Not Found
```json
{
  "message": "Resource not found"
}
```

### 500 Internal Server Error
```json
{
  "message": "Server error",
  "error": "Error details"
}
```

## Rate Limiting

The API implements rate limiting:
- **Default**: 60 requests per minute per user
- Rate limit headers are included in all responses:
  - `X-RateLimit-Limit`: Maximum requests allowed
  - `X-RateLimit-Remaining`: Remaining requests
  - `X-RateLimit-Reset`: Time when limit resets (Unix timestamp)

## Pagination

List endpoints support pagination:

**Query Parameters:**
- `page` (int) - Page number (default: 1)
- `per_page` (int) - Items per page (default: 15, max: 100)

**Response Format:**
```json
{
  "data": [...],
  "links": {
    "first": "url",
    "last": "url",
    "prev": null,
    "next": "url"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

## Webhooks

The system supports webhooks for real-time notifications:

### Available Events
- `sale.created` - New sale created
- `sale.refunded` - Sale refunded
- `inventory.low_stock` - Product stock below threshold
- `customer.created` - New customer registered

### Webhook Payload
```json
{
  "event": "sale.created",
  "timestamp": "2024-01-01T10:00:00Z",
  "data": {
    "id": 1,
    "reference": "SALE-001",
    "total": 105.00
  }
}
```

Configure webhooks in the admin panel under Settings > Webhooks.

## Best Practices

1. **Always use HTTPS** in production
2. **Store tokens securely** - Never expose in client-side code
3. **Handle rate limits** - Implement exponential backoff
4. **Use pagination** - Don't fetch all records at once
5. **Validate input** - Always validate before sending requests
6. **Handle errors gracefully** - Implement proper error handling
7. **Cache responses** - Cache when appropriate to reduce API calls
8. **Use appropriate HTTP methods** - GET for reads, POST for creates, PUT for updates, DELETE for deletes

## Support

For API support, contact: api-support@possystem.com
