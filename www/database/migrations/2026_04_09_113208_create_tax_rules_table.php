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
        Schema::create('tax_rules', function (Blueprint $table) {
            $table->id();
            $table->string('fiscal_regime')->default('SIMPLES_NACIONAL')->comment('Ex: SIMPLES_NACIONAL, LUCRO_REAL');
            $table->string('ncm')->nullable()->comment('Aplicar regra para um NCM específico');
            $table->string('uf_origin', 2)->nullable();
            $table->string('uf_destination', 2)->nullable();
            $table->string('cfop', 4)->nullable();
            
            // Alíquotas base
            $table->decimal('icms_rate', 5, 2)->default(0);
            $table->decimal('icms_st_margin', 5, 2)->default(0)->comment('MVA/IVA-ST %');
            $table->boolean('has_st')->default(false);
            
            $table->string('cst_csosn', 4)->nullable();
            $table->decimal('pis_rate', 5, 2)->default(0);
            $table->decimal('cofins_rate', 5, 2)->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Índices de otimização de busca porque motor tributário é varrido em toda venda
            $table->index(['ncm', 'uf_origin', 'uf_destination']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rules');
    }
};
