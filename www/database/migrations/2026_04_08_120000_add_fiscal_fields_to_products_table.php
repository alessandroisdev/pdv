<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('ncm_code')->default('99999999')->after('category_id');
            $table->string('cfop_code')->default('5102')->after('ncm_code');
            $table->string('cest_code')->nullable()->after('cfop_code');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['ncm_code', 'cfop_code', 'cest_code']);
        });
    }
};
