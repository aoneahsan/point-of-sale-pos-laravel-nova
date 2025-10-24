<?php

/**
 * This script implements all remaining migration schemas
 * Run with: php implement_migrations.php
 */

$migrations = [
    'purchase_orders' => <<<'SCHEMA'
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // User who created
            $table->string('reference')->unique();
            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->enum('status', ['pending', 'ordered', 'received', 'cancelled'])->default('pending');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['supplier_id', 'store_id', 'status']);
            $table->index('reference');
            $table->index(['order_date', 'expected_date']);
        });
SCHEMA,

    'purchase_order_items' => <<<'SCHEMA'
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->constrained()->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total', 10, 2);
            $table->integer('received_quantity')->default(0);
            $table->timestamps();

            $table->index(['purchase_order_id', 'product_variant_id']);
        });
SCHEMA,

    'stock_movements' => <<<'SCHEMA'
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['sale', 'return', 'adjustment', 'purchase', 'transfer_in', 'transfer_out']);
            $table->integer('quantity'); // Positive for additions, negative for deductions
            $table->string('reference')->nullable(); // Related transaction reference
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_variant_id', 'store_id', 'type']);
            $table->index('created_at');
        });
SCHEMA,

    'stock_adjustments' => <<<'SCHEMA'
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // User who created
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reference')->unique();
            $table->enum('reason', ['damaged', 'theft', 'loss', 'found', 'recount', 'other']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['store_id', 'status']);
            $table->index('reference');
        });
SCHEMA,

    'stock_adjustment_items' => <<<'SCHEMA'
        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->constrained()->onDelete('restrict');
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->integer('difference');
            $table->timestamps();

            $table->index(['stock_adjustment_id', 'product_variant_id']);
        });
SCHEMA,

    'customer_groups' => <<<'SCHEMA'
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('active');
        });
SCHEMA,

    'customers' => <<<'SCHEMA'
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_group_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->integer('loyalty_points')->default(0);
            $table->decimal('store_credit', 10, 2)->default(0);
            $table->date('date_of_birth')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_group_id', 'name']);
            $table->index(['email', 'phone']);
        });
SCHEMA,

    'payment_methods' => <<<'SCHEMA'
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['cash', 'card', 'digital_wallet', 'store_credit', 'other']);
            $table->boolean('active')->default(true);
            $table->json('settings')->nullable(); // For payment gateway settings
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'active']);
        });
SCHEMA,

    'tax_rates' => <<<'SCHEMA'
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('rate', 5, 2); // Percentage
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('active');
        });
SCHEMA,

    'sales' => <<<'SCHEMA'
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // Cashier
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reference')->unique();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['completed', 'pending', 'on_hold', 'cancelled', 'refunded'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['store_id', 'user_id', 'status']);
            $table->index('customer_id');
            $table->index('reference');
            $table->index('created_at');
        });
SCHEMA,

    'sale_items' => <<<'SCHEMA'
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->constrained()->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();

            $table->index(['sale_id', 'product_variant_id']);
        });
SCHEMA,

    'sale_payments' => <<<'SCHEMA'
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->string('reference')->nullable(); // Payment gateway reference
            $table->timestamps();

            $table->index(['sale_id', 'payment_method_id']);
        });
SCHEMA,

    'returns' => <<<'SCHEMA'
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('restrict');
            $table->foreignId('store_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // User who processed
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reference')->unique();
            $table->string('reason');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sale_id', 'store_id', 'status']);
            $table->index('reference');
        });
SCHEMA,

    'return_items' => <<<'SCHEMA'
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_item_id')->constrained()->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();

            $table->index(['return_id', 'sale_item_id']);
        });
SCHEMA,

    'discounts' => <<<'SCHEMA'
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['percentage', 'fixed', 'buy_x_get_y', 'bundle']);
            $table->decimal('value', 10, 2); // Percentage or fixed amount
            $table->decimal('min_amount', 10, 2)->nullable(); // Minimum purchase amount
            $table->integer('max_uses')->nullable(); // Maximum times this discount can be used
            $table->integer('uses')->default(0); // Current usage count
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->json('conditions')->nullable(); // Additional conditions (products, categories, etc.)
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['active', 'start_date', 'end_date']);
        });
SCHEMA,

    'coupons' => <<<'SCHEMA'
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->integer('max_uses')->nullable();
            $table->integer('uses')->default(0);
            $table->integer('max_uses_per_customer')->default(1);
            $table->dateTime('expires_at')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['code', 'active']);
            $table->index('expires_at');
        });
SCHEMA,

    'cash_drawers' => <<<'SCHEMA'
        Schema::create('cash_drawers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // Cashier
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_cash', 10, 2);
            $table->decimal('expected_cash', 10, 2)->nullable(); // Calculated on close
            $table->decimal('actual_cash', 10, 2)->nullable(); // Counted on close
            $table->decimal('difference', 10, 2)->nullable(); // Actual - Expected
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['store_id', 'user_id', 'status']);
            $table->index(['opened_at', 'closed_at']);
        });
SCHEMA,

    'cash_transactions' => <<<'SCHEMA'
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_drawer_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['in', 'out']); // Cash in or out
            $table->decimal('amount', 10, 2);
            $table->string('reference')->nullable();
            $table->string('reason'); // Bank deposit, expense, change, etc.
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['cash_drawer_id', 'type']);
        });
SCHEMA,

    'settings' => <<<'SCHEMA'
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade'); // NULL for global settings
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['store_id', 'key']);
            $table->index('key');
        });
SCHEMA,

    'receipts' => <<<'SCHEMA'
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->text('template')->nullable(); // Custom receipt template
            $table->timestamp('printed_at')->nullable();
            $table->timestamp('emailed_at')->nullable();
            $table->timestamps();

            $table->index('sale_id');
        });
SCHEMA,

    'transactions' => <<<'SCHEMA'
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('transactionable'); // Polymorphic relation (sales, returns, etc.)
            $table->enum('type', ['payment', 'refund', 'credit', 'debit']);
            $table->decimal('amount', 10, 2);
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['transactionable_type', 'transactionable_id']);
            $table->index(['type', 'created_at']);
        });
SCHEMA,
];

echo "Migration schemas ready to implement!\n";
echo "Total migrations: " . count($migrations) . "\n\n";

foreach ($migrations as $table => $schema) {
    echo "✓ {$table}\n";
}

echo "\nAll schemas are ready!\n";
