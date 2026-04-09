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
        Schema::create('crm_opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Ex: Negociação Trator 300x');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('branch_id'); // Isolamento Multi-Branch
            $table->unsignedBigInteger('owner_id')->nullable()->comment('Funcionário responsável pelo Lead');
            $table->string('stage')->default('PROSPECT')->comment('PROSPECT, NEGOTIATION, CONTRACT, WON, LOST');
            $table->bigInteger('amount_cents')->default(0);
            $table->text('notes')->nullable();
            $table->date('expected_close_date')->nullable();
            
            // Ordering in the Kanban lane
            $table->integer('order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('branch_id');
            $table->index('stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_opportunities');
    }
};
