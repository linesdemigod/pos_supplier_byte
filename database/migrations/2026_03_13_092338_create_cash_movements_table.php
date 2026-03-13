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
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('shift_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->string('type'); // 'cash in' or 'cash out'
            $table->decimal('amount', 10, 2);
            $table->string('reason')->nullable();
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};
