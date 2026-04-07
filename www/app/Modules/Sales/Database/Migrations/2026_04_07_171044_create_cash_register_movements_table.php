<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_register_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_id')->constrained('cash_registers')->cascadeOnDelete();
            $table->enum('type', ['SANGRIA', 'REFORCO']);
            $table->bigInteger('amount_cents');
            $table->string('reason')->nullable();
            
            // Employee PIN linking who authorized this physical withdrawal
            $table->string('authorized_by_pin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_register_movements');
    }
};
