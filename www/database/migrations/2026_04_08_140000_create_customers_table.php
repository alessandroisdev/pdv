<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document')->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('birth_date')->nullable();
            $table->integer('points')->default(0);
            $table->date('last_purchase_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
