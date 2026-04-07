<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('price_cents_club')->nullable()->after('price_cents_sale');
        });
        
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('seller_type');
            $table->string('customer_document')->nullable()->after('customer_id');

            // Foreign Key if CRM is active
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_id', 'customer_document']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price_cents_club');
        });
    }
};
