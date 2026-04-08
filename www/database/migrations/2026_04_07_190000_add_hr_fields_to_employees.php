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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('cpf')->nullable()->unique()->after('status');
            $table->string('rg')->nullable()->after('cpf');
            $table->date('admission_date')->nullable()->after('rg');
            $table->integer('base_salary_cents')->default(0)->after('admission_date');
            $table->string('role_description')->nullable()->after('base_salary_cents');
            $table->string('contact_phone')->nullable()->after('role_description');
            $table->text('address')->nullable()->after('contact_phone');
            $table->string('bank_account_info')->nullable()->after('address');
            $table->string('work_schedule')->nullable()->after('bank_account_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'cpf',
                'rg',
                'admission_date',
                'base_salary_cents',
                'role_description',
                'contact_phone',
                'address',
                'bank_account_info',
                'work_schedule'
            ]);
        });
    }
};
