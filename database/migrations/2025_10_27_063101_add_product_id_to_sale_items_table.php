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
        Schema::table('sale_items', function (Blueprint $table) {
            // Add product_id column
            $table->foreignId('product_id')->after('sale_id')->nullable()->constrained()->onDelete('restrict');

            // Add unit_cost for profit calculation
            $table->decimal('unit_cost', 10, 2)->after('unit_price')->default(0);

            $table->index(['sale_id', 'product_id']);
        });

        // In a separate schema block, modify product_variant_id to be nullable
        Schema::table('sale_items', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['product_variant_id']);

            // Make column nullable
            $table->unsignedBigInteger('product_variant_id')->nullable()->change();

            // Recreate foreign key
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropIndex(['sale_id', 'product_id']);
            $table->dropColumn(['product_id', 'unit_cost']);

            // Restore product_variant_id to not nullable
            $table->dropForeign(['product_variant_id']);
            $table->foreignId('product_variant_id')->change();
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('restrict');
            $table->index(['sale_id', 'product_variant_id']);
        });
    }
};
