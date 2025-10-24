#!/bin/bash

# Fix migration timestamps to ensure proper ordering based on foreign key dependencies

cd database/migrations

# Base tables - no foreign keys (timestamp 143300)
mv 2025_10_24_143219_create_stores_table.php 2025_10_24_143300_create_stores_table.php 2>/dev/null
mv 2025_10_24_143219_create_brands_table.php 2025_10_24_143301_create_brands_table.php 2>/dev/null
mv 2025_10_24_143220_create_categories_table.php 2025_10_24_143302_create_categories_table.php 2>/dev/null
mv 2025_10_24_143230_create_suppliers_table.php 2025_10_24_143303_create_suppliers_table.php 2>/dev/null
mv 2025_10_24_143239_create_customer_groups_table.php 2025_10_24_143304_create_customer_groups_table.php 2>/dev/null
mv 2025_10_24_143239_create_payment_methods_table.php 2025_10_24_143305_create_payment_methods_table.php 2>/dev/null
mv 2025_10_24_143248_create_tax_rates_table.php 2025_10_24_143306_create_tax_rates_table.php 2>/dev/null

# Products (depends on categories, brands, tax_rates)
mv 2025_10_24_143220_create_products_table.php 2025_10_24_143310_create_products_table.php 2>/dev/null

# Product variants (depends on products, stores)
mv 2025_10_24_143221_create_product_variants_table.php 2025_10_24_143311_create_product_variants_table.php 2>/dev/null

# Product images (depends on products)
mv 2025_10_24_143229_create_product_images_table.php 2025_10_24_143312_create_product_images_table.php 2>/dev/null

# Purchase orders (depends on suppliers, stores)
mv 2025_10_24_143230_create_purchase_orders_table.php 2025_10_24_143320_create_purchase_orders_table.php 2>/dev/null

# Purchase order items (depends on purchase_orders, product_variants)
mv 2025_10_24_143230_create_purchase_order_items_table.php 2025_10_24_143321_create_purchase_order_items_table.php 2>/dev/null

# Customers (depends on stores, customer_groups)
mv 2025_10_24_143239_create_customers_table.php 2025_10_24_143330_create_customers_table.php 2>/dev/null

# Sales (depends on stores, users, customers)
mv 2025_10_24_143248_create_sales_table.php 2025_10_24_143340_create_sales_table.php 2>/dev/null

# Sale items (depends on sales, product_variants)
mv 2025_10_24_143248_create_sale_items_table.php 2025_10_24_143341_create_sale_items_table.php 2>/dev/null

# Sale payments (depends on sales, payment_methods)
mv 2025_10_24_143248_create_sale_payments_table.php 2025_10_24_143342_create_sale_payments_table.php 2>/dev/null

# Returns (depends on sales, stores)
mv 2025_10_24_143248_create_returns_table.php 2025_10_24_143343_create_returns_table.php 2>/dev/null

# Return items (depends on returns, product_variants)
mv 2025_10_24_143258_create_return_items_table.php 2025_10_24_143344_create_return_items_table.php 2>/dev/null

# Stock movements (depends on product_variants, stores)
mv 2025_10_24_143230_create_stock_movements_table.php 2025_10_24_143350_create_stock_movements_table.php 2>/dev/null

# Stock adjustments (depends on stores)
mv 2025_10_24_143238_create_stock_adjustments_table.php 2025_10_24_143351_create_stock_adjustments_table.php 2>/dev/null

# Stock adjustment items (depends on stock_adjustments, product_variants)
mv 2025_10_24_143239_create_stock_adjustment_items_table.php 2025_10_24_143352_create_stock_adjustment_items_table.php 2>/dev/null

# Discounts (depends on stores)
mv 2025_10_24_143258_create_discounts_table.php 2025_10_24_143360_create_discounts_table.php 2>/dev/null

# Coupons (depends on stores)
mv 2025_10_24_143258_create_coupons_table.php 2025_10_24_143361_create_coupons_table.php 2>/dev/null

# Cash drawers (depends on stores, users)
mv 2025_10_24_143258_create_cash_drawers_table.php 2025_10_24_143370_create_cash_drawers_table.php 2>/dev/null

# Cash transactions (depends on cash_drawers)
mv 2025_10_24_143259_create_cash_transactions_table.php 2025_10_24_143371_create_cash_transactions_table.php 2>/dev/null

# Settings (depends on stores)
mv 2025_10_24_143259_create_settings_table.php 2025_10_24_143380_create_settings_table.php 2>/dev/null

# Receipts (depends on sales)
mv 2025_10_24_143259_create_receipts_table.php 2025_10_24_143381_create_receipts_table.php 2>/dev/null

# Transactions (depends on stores)
mv 2025_10_24_143259_create_transactions_table.php 2025_10_24_143382_create_transactions_table.php 2>/dev/null

echo "Migration timestamps fixed successfully!"
