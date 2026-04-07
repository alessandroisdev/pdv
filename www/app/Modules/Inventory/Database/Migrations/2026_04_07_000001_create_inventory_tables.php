<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            
            // Strict Object Calisthenics: Always cents for currency
            $table->bigInteger('price_cents_cost')->default(0);
            $table->bigInteger('price_cents_sale')->default(0);
            
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            
            // Polymorphic relation to who made the movement (User or FunctionalProfile)
            $table->morphs('actor'); 
            
            $table->integer('quantity'); // Signed integer (+ means IN, - means OUT)
            $table->enum('type', ['IN', 'OUT', 'ADJUSTMENT', 'LOSS', 'SALE_REFUND']);
            $table->string('transaction_motive')->nullable(); 
            
            // Move tracking is append-only
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
