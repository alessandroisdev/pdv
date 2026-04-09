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
            $table->string('gateway_id')->nullable()->after('status')->comment('ID da cobrança no Asaas/Stripe');
            $table->string('gateway_url')->nullable()->after('gateway_id');
            $table->text('pix_payload')->nullable()->after('gateway_url')->comment('Copia e Cola EMV');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropColumn(['gateway_id', 'gateway_url', 'pix_payload']);
        });
    }
};
