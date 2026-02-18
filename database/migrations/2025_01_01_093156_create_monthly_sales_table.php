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
        Schema::create('monthly_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('store_id')->nullable()->constrained()->onUpdate('cascade')->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->double('total_sales')->nullable();
            $table->dateTime('open_date');
            $table->dateTime('close_date')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->index(['id', 'user_id', 'store_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_sales');
    }
};
