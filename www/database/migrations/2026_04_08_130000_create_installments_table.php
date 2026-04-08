<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // PAYABLE (A Pagar), RECEIVABLE (A Receber)
            $table->string('description'); // Ex: Conta de Luz Abril/2026
            $table->integer('amount_cents');
            $table->date('due_date'); // Data de Vencimento
            $table->date('paid_date')->nullable(); // Data em que foi pago realmente
            $table->string('status')->default('PENDING'); // PENDING, PAID
            
            // Relacionamento Opcional: Para atrelarmos isto a Transações na Hora de dar Baixa
            $table->unsignedBigInteger('transaction_id')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('installments');
    }
};
