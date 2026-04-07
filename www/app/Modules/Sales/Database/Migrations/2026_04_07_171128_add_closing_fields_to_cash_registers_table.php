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
        if (!Schema::hasColumn('cash_registers', 'reported_cents')) {
            Schema::table('cash_registers', function (Blueprint $table) {
                $table->bigInteger('reported_cents')->nullable()->after('initial_cents');
                $table->bigInteger('difference_cents')->nullable()->after('reported_cents');
                $table->timestamp('closed_at')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        Schema::table('cash_registers', function (Blueprint $table) {
            $table->dropColumn(['reported_cents', 'difference_cents', 'closed_at']);
        });
    }
};
