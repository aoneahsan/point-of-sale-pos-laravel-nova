<?php

/**
 * This script automatically updates all migration files with their proper schemas
 * Run: php apply_migrations_script.php
 */

$migrationSchemas = [
    'stock_movements' => "
            \$table->id();
            \$table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
            \$table->foreignId('store_id')->constrained()->onDelete('cascade');
            \$table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            \$table->enum('type', ['sale', 'return', 'adjustment', 'purchase', 'transfer_in', 'transfer_out']);
            \$table->integer('quantity'); // Positive for additions, negative for deductions
            \$table->string('reference')->nullable();
            \$table->text('notes')->nullable();
            \$table->timestamps();

            \$table->index(['product_variant_id', 'store_id', 'type']);
            \$table->index('created_at');",

    'stock_adjustments' => "
            \$table->id();
            \$table->foreignId('store_id')->constrained()->onDelete('cascade');
            \$table->foreignId('user_id')->constrained()->onDelete('restrict');
            \$table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            \$table->string('reference')->unique();
            \$table->enum('reason', ['damaged', 'theft', 'loss', 'found', 'recount', 'other']);
            \$table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            \$table->text('notes')->nullable();
            \$table->timestamp('approved_at')->nullable();
            \$table->timestamps();
            \$table->softDeletes();

            \$table->index(['store_id', 'status']);
            \$table->index('reference');",

    'stock_adjustment_items' => "
            \$table->id();
            \$table->foreignId('stock_adjustment_id')->constrained()->onDelete('cascade');
            \$table->foreignId('product_variant_id')->constrained()->onDelete('restrict');
            \$table->integer('quantity_before');
            \$table->integer('quantity_after');
            \$table->integer('difference');
            \$table->timestamps();

            \$table->index(['stock_adjustment_id', 'product_variant_id']);",

    'customer_groups' => "
            \$table->id();
            \$table->string('name');
            \$table->decimal('discount_percentage', 5, 2)->default(0);
            \$table->text('description')->nullable();
            \$table->boolean('active')->default(true);
            \$table->timestamps();
            \$table->softDeletes();

            \$table->index('active');",

    'customers' => "
            \$table->id();
            \$table->foreignId('customer_group_id')->nullable()->constrained()->onDelete('set null');
            \$table->string('name');
            \$table->string('email')->nullable()->unique();
            \$table->string('phone')->nullable();
            \$table->text('address')->nullable();
            \$table->integer('loyalty_points')->default(0);
            \$table->decimal('store_credit', 10, 2)->default(0);
            \$table->date('date_of_birth')->nullable();
            \$table->text('notes')->nullable();
            \$table->timestamps();
            \$table->softDeletes();

            \$table->index(['customer_group_id', 'name']);
            \$table->index(['email', 'phone']);",

    'payment_methods' => "
            \$table->id();
            \$table->string('name');
            \$table->enum('type', ['cash', 'card', 'digital_wallet', 'store_credit', 'other']);
            \$table->boolean('active')->default(true);
            \$table->json('settings')->nullable();
            \$table->timestamps();
            \$table->softDeletes();

            \$table->index(['type', 'active']);",

    'tax_rates' => "
            \$table->id();
            \$table->string('name');
            \$table->decimal('rate', 5, 2);
            \$table->text('description')->nullable();
            \$table->boolean('active')->default(true);
            \$table->timestamps();
            \$table->softDeletes();

            \$table->index('active');",

    'sales' => "
            \$table->id();
            \$table->foreignId('store_id')->constrained()->onDelete('restrict');
            \$table->foreignId('user_id')->constrained()->onDelete('restrict');
            \$table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            \$table->string('reference')->unique();
            \$table->decimal('subtotal', 10, 2);
            \$table->decimal('tax', 10, 2)->default(0);
            \$table->decimal('discount', 10, 2)->default(0);
            \$table->decimal('total', 10, 2);
            \$table->enum('status', ['completed', 'pending', 'on_hold', 'cancelled', 'refunded'])->default('completed');
            \$table->text('notes')->nullable();
            \$table->timestamps();
            \$table->softDeletes();

            \$table->index(['store_id', 'user_id', 'status']);
            \$table->index('customer_id');
            \$table->index('reference');
            \$table->index('created_at');",

    'sale_items' => "
            \$table->id();
            \$table->foreignId('sale_id')->constrained()->onDelete('cascade');
            \$table->foreignId('product_variant_id')->constrained()->onDelete('restrict');
            \$table->integer('quantity');
            \$table->decimal('unit_price', 10, 2);
            \$table->decimal('discount', 10, 2)->default(0);
            \$table->decimal('tax', 10, 2)->default(0);
            \$table->decimal('total', 10, 2);
            \$table->timestamps();

            \$table->index(['sale_id', 'product_variant_id']);",

    'sale_payments' => "
            \$table->id();
            \$table->foreignId('sale_id')->constrained()->onDelete('cascade');
            \$table->foreignId('payment_method_id')->constrained()->onDelete('restrict');
            \$table->decimal('amount', 10, 2);
            \$table->string('reference')->nullable();
            \$table->timestamps();

            \$table->index(['sale_id', 'payment_method_id']);",

    'returns' => "
            \$table->id();
            \$table->foreignId('sale_id')->constrained()->onDelete('restrict');
            \$table->foreignId('store_id')->constrained()->onDelete('restrict');
            \$table->foreignId('user_id')->constrained()->onDelete('restrict');
            \$table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            \$table->string('reference')->unique();
            \$table->string('reason');
            \$table->decimal('subtotal', 10, 2);
            \$table->decimal('tax', 10, 2)->default(0);
            \$table->decimal('total', 10, 2);
            \$table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            \$table->text('notes')->nullable();
            \$table->timestamp('approved_at')->nullable();
            \$table->timestamps();
            \$table->softDeletes();

            \$table->index(['sale_id', 'store_id', 'status']);
            \$table->index('reference');",

    'return_items' => "
            \$table->id();
            \$table->foreignId('return_id')->constrained()->onDelete('cascade');
            \$table->foreignId('sale_item_id')->constrained()->onDelete('restrict');
            \$table->integer('quantity');
            \$table->decimal('unit_price', 10, 2);
            \$table->decimal('total', 10, 2);
            \$table->timestamps();

            \$table->index(['return_id', 'sale_item_id']);",

    'discounts' => "
            \$table->id();
            \$table->string('name');
            \$table->enum('type', ['percentage', 'fixed', 'buy_x_get_y', 'bundle']);
            \$table->decimal('value', 10, 2);
            \$table->decimal('min_amount', 10, 2)->nullable();
            \$table->integer('max_uses')->nullable();
            \$table->integer('uses')->default(0);
            \$table->dateTime('start_date')->nullable();
            \$table->dateTime('end_date')->nullable();
            \$table->json('conditions')->nullable();
            \$table->boolean('active')->default(true);
            \$table->timestamps();
            \$table->softDeletes();

            \$table->index(['active', 'start_date', 'end_date']);",

    'coupons' => "
            \$table->id();
            \$table->foreignId('discount_id')->constrained()->onDelete('cascade');
            \$table->string('code')->unique();
            \$table->integer('max_uses')->nullable();
            \$table->integer('uses')->default(0);
            \$table->integer('max_uses_per_customer')->default(1);
            \$table->dateTime('expires_at')->nullable();
            \$table->boolean('active')->default(true);
            \$table->timestamps();
            \$table->softDeletes();

            \$table->index(['code', 'active']);
            \$table->index('expires_at');",

    'cash_drawers' => "
            \$table->id();
            \$table->foreignId('store_id')->constrained()->onDelete('cascade');
            \$table->foreignId('user_id')->constrained()->onDelete('restrict');
            \$table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
            \$table->timestamp('opened_at');
            \$table->timestamp('closed_at')->nullable();
            \$table->decimal('opening_cash', 10, 2);
            \$table->decimal('expected_cash', 10, 2)->nullable();
            \$table->decimal('actual_cash', 10, 2)->nullable();
            \$table->decimal('difference', 10, 2)->nullable();
            \$table->enum('status', ['open', 'closed'])->default('open');
            \$table->text('notes')->nullable();
            \$table->timestamps();

            \$table->index(['store_id', 'user_id', 'status']);
            \$table->index(['opened_at', 'closed_at']);",

    'cash_transactions' => "
            \$table->id();
            \$table->foreignId('cash_drawer_id')->constrained()->onDelete('cascade');
            \$table->enum('type', ['in', 'out']);
            \$table->decimal('amount', 10, 2);
            \$table->string('reference')->nullable();
            \$table->string('reason');
            \$table->text('notes')->nullable();
            \$table->timestamps();

            \$table->index(['cash_drawer_id', 'type']);",

    'settings' => "
            \$table->id();
            \$table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
            \$table->string('key');
            \$table->text('value')->nullable();
            \$table->timestamps();

            \$table->unique(['store_id', 'key']);
            \$table->index('key');",

    'receipts' => "
            \$table->id();
            \$table->foreignId('sale_id')->constrained()->onDelete('cascade');
            \$table->text('template')->nullable();
            \$table->timestamp('printed_at')->nullable();
            \$table->timestamp('emailed_at')->nullable();
            \$table->timestamps();

            \$table->index('sale_id');",

    'transactions' => "
            \$table->id();
            \$table->morphs('transactionable');
            \$table->enum('type', ['payment', 'refund', 'credit', 'debit']);
            \$table->decimal('amount', 10, 2);
            \$table->string('reference')->nullable();
            \$table->text('notes')->nullable();
            \$table->timestamps();

            \$table->index(['transactionable_type', 'transactionable_id']);
            \$table->index(['type', 'created_at']);",
];

$migrationsDir = __DIR__ . '/database/migrations/';
$files = glob($migrationsDir . '*_create_*_table.php');

$updated = 0;
$skipped = 0;

foreach ($files as $file) {
    $basename = basename($file);

    // Extract table name from filename
    preg_match('/_create_(.+)_table\.php$/', $basename, $matches);
    if (!isset($matches[1])) {
        continue;
    }

    $tableName = $matches[1];

    if (!isset($migrationSchemas[$tableName])) {
        $skipped++;
        continue;
    }

    $content = file_get_contents($file);

    // Check if already updated (has more than just id and timestamps)
    if (strpos($content, "->constrained()") !== false || strpos($content, "foreignId") !== false) {
        echo "⏭️  Skipping (already updated): {$tableName}\n";
        $skipped++;
        continue;
    }

    // Find the schema closure and replace it
    $pattern = '/(Schema::create\(\'' . $tableName . '\', function \(Blueprint \$table\) \{)\s*\$table->id\(\);\s*\$table->timestamps\(\);\s*(\}\);)/s';

    $replacement = '$1' . $migrationSchemas[$tableName] . '$2';

    $newContent = preg_replace($pattern, $replacement, $content);

    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        echo "✅ Updated: {$tableName}\n";
        $updated++;
    } else {
        echo "❌ Failed to update: {$tableName}\n";
    }
}

echo "\n";
echo "========================================\n";
echo "Updated: {$updated} migrations\n";
echo "Skipped: {$skipped} migrations\n";
echo "========================================\n";
