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
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_drawer_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['in', 'out']);
            $table->decimal('amount', 10, 2);
            $table->string('reference')->nullable();
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['cash_drawer_id', 'type']);});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
