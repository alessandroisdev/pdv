<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('customers')) {
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
        } else {
            Schema::table('customers', function (Blueprint $table) {
                if (!Schema::hasColumn('customers', 'document')) {
                    $table->string('document')->nullable()->unique();
                }
                if (!Schema::hasColumn('customers', 'points')) {
                    $table->integer('points')->default(0);
                }
                if (!Schema::hasColumn('customers', 'last_purchase_date')) {
                    $table->date('last_purchase_date')->nullable();
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
