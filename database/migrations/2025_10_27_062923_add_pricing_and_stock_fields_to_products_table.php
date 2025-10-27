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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('store_id')->after('id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2)->after('track_inventory')->default(0);
            $table->decimal('cost', 10, 2)->after('price')->default(0);
            $table->integer('stock_quantity')->after('cost')->default(0);
            $table->integer('reorder_point')->after('stock_quantity')->default(10);
            $table->boolean('track_stock')->after('reorder_point')->default(true);

            $table->index(['store_id', 'active']);
            $table->index('stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropIndex(['store_id', 'active']);
            $table->dropIndex(['stock_quantity']);
            $table->dropColumn(['store_id', 'price', 'cost', 'stock_quantity', 'reorder_point', 'track_stock']);
        });
    }
};
