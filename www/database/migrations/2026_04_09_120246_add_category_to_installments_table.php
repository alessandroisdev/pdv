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
        Schema::table('installments', function (Blueprint $table) {
            // Categoria do DRE (Ex: INCOME_OPERATIONAL, COGS, OPEX_HR, OPEX_MARKETING, CAPEX, TAXES)
            $table->string('dre_category')->nullable()->default('OPEX_GENERAL')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropColumn('dre_category');
        });
    }
};
