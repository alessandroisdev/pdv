<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fiscal_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->string('model', 2)->default('65'); // 65 para NFC-e, 55 para NF-e
            $table->integer('series')->default(1);
            $table->bigInteger('number');
            $table->longText('xml_signed')->nullable();
            $table->longText('xml_authorized')->nullable();
            $table->longText('xml_canceled')->nullable();
            $table->string('status', 30)->default('PENDENTE'); // PENDENTE, AUTORIZADO, REJEITADO, CANCELADO
            $table->string('access_key', 44)->nullable()->unique();
            $table->string('receipt_protocol', 50)->nullable();
            $table->string('message')->nullable();
            $table->string('type', 20)->default('NORMAL');
            $table->tinyInteger('environment')->default(2); // 1 = Prod, 2 = Homologação
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fiscal_documents');
    }
};
