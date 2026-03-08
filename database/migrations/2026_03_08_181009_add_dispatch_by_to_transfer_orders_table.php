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
        Schema::table('transfer_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('dispatched_by')->nullable()->after('status');
            $table->unsignedBigInteger('accepted_by')->nullable()->after('dispatched_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_orders', function (Blueprint $table) {
            $table->dropColumn('dispatched_by');
            $table->dropColumn('accepted_by');

        });
    }
};
