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
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained()->onUpdate('cascade')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->onUpdate('cascade')->nullOnDelete();
            $table->decimal('price', 12, 2);
            $table->decimal('total', 12, 2);
            $table->integer('quantity');
            $table->string('reference')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('return_date')->nullable();
            $table->longtext('reason');
            $table->timestamps();
            $table->index(['id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};
