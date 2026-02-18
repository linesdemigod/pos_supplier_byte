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
        Schema::create('item_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->nullable()->constrained()->cascadeOnDelete()->onDelete('set null');
            $table->foreignId('warehouse_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('movement_type');
            $table->date('movement_date');
            $table->string('status')->default('outgoing');
            $table->timestamps();
            $table->index(['id', 'item_id', 'store_id', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_movements');
    }
};
