<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('daily_sale_id')->nullable()->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('monthly_sale_id')->nullable()->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('store_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->decimal('discount', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('grandtotal', 12, 2);
            $table->string('payment_method');
            $table->string('reference');
            $table->timestamps();
            $table->index(['id', 'user_id', 'customer_id', 'store_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
