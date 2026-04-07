<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('actor'); // Who registered the transaction
            $table->enum('type', ['INCOME', 'EXPENSE']);
            $table->bigInteger('amount_cents');
            $table->string('payment_method')->nullable();
            
            // Polymorphic link to source (e.g. Sale, PurchaseOrder)
            $table->nullableMorphs('source');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
