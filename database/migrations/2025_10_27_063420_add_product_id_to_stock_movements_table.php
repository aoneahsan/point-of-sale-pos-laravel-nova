<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Add product_id
            $table->foreignId('product_id')->after('id')->nullable()->constrained()->onDelete('cascade');

            // Make product_variant_id nullable
            $table->dropForeign(['product_variant_id']);
            $table->dropIndex(['product_variant_id', 'store_id', 'type']);
            $table->foreignId('product_variant_id')->nullable()->change();
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');

            // Add quantity tracking fields
            $table->integer('quantity_before')->after('quantity')->default(0);
            $table->integer('quantity_after')->after('quantity_before')->default(0);

            // Update type enum to include more types
            $table->enum('type', ['in', 'out', 'adjustment', 'sale', 'return', 'purchase', 'transfer_in', 'transfer_out'])->change();

            // Add polymorphic relation for relatable entities
            $table->string('relatable_type')->nullable()->after('reference');
            $table->unsignedBigInteger('relatable_id')->nullable()->after('relatable_type');

            // Update indexes
            $table->index(['product_id', 'store_id', 'type']);
            $table->index(['relatable_type', 'relatable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropIndex(['product_id', 'store_id', 'type']);
            $table->dropIndex(['relatable_type', 'relatable_id']);
            $table->dropColumn(['product_id', 'quantity_before', 'quantity_after', 'relatable_type', 'relatable_id']);

            // Restore product_variant_id to not nullable
            $table->dropForeign(['product_variant_id']);
            $table->foreignId('product_variant_id')->change();
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->index(['product_variant_id', 'store_id', 'type']);

            // Restore original type enum
            $table->enum('type', ['sale', 'return', 'adjustment', 'purchase', 'transfer_in', 'transfer_out'])->change();
        });
    }
};
