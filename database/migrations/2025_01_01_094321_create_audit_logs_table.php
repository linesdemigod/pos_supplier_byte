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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained()->onUpdate('cascade')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->onUpdate('cascade')->nullOnDelete();
            $table->string('ip_address');
            $table->string('description');
            $table->json('data_before')->nullable();
            $table->json('data_after')->nullable();
            $table->timestamps();
            $table->index(['id', 'user_id', 'warehouse_id', 'store_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
