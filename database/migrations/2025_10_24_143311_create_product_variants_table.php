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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('name')->default('Default'); // Variant name (e.g., "Small", "Red")
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('low_stock_threshold')->default(10);
            $table->string('image')->nullable();
            $table->json('attributes')->nullable(); // For variant-specific attributes
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'store_id']);
            $table->index(['sku', 'barcode']);
            $table->index('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
