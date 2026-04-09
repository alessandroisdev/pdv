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
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('id');
            $table->index('branch_id');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('id');
            $table->index('branch_id');
        });

        Schema::table('cash_registers', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('id');
            $table->index('branch_id');
        });

        // E se for multitenancy profundo, adicionamos em users/employees também.
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('cash_registers', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
};
