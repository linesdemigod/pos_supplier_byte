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
        // Drop daily_sale_id if it exists
        if (Schema::hasColumn('sales', 'daily_sale_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropForeign(['daily_sale_id']);
                $table->dropColumn('daily_sale_id');
            });
        }

        // Drop monthly_sale_id if it exists
        if (Schema::hasColumn('sales', 'monthly_sale_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropForeign(['monthly_sale_id']);
                $table->dropColumn('monthly_sale_id');
            });
        }

        // Add new fields
        Schema::table('sales', function (Blueprint $table) {


            $table->string('payment_status')
                ->default('paid')
                ->after('reference');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {

            // Add back dropped fields (no foreign keys since you didn’t define any)
            $table->unsignedBigInteger('daily_sale_id')->nullable();
            $table->unsignedBigInteger('monthly_sale_id')->nullable();

            $table->dropColumn('payment_status');
        });
    }

};
