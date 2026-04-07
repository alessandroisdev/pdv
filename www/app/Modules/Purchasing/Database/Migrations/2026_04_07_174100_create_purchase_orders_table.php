<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('restrict');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('status')->default('PENDING'); // PENDING, RECEIVED, CANCELLED
                $table->string('invoice_number')->nullable(); // NFe
                $table->bigInteger('total_cents')->default(0);
                $table->text('notes')->nullable();
                $table->timestamp('received_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
