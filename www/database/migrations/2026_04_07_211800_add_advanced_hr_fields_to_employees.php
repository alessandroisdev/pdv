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
            // Personal & Documentation
            $table->date('birth_date')->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->string('rg_issuer', 50)->nullable();
            $table->string('pis_pasep', 50)->nullable();
            $table->string('ctps_number', 50)->nullable();
            
            // Advanced Address
            $table->string('cep', 20)->nullable();
            $table->string('city')->nullable();
            $table->string('state', 10)->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('address_number', 20)->nullable();
            
            // Corporate & Professional details
            $table->string('department')->nullable();
            $table->string('contract_type')->nullable()->comment('CLT, PJ, Estagiario');
            
            // Banking extensions
            $table->string('pix_key')->nullable();
            
            // Emergency Contacts
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'birth_date', 'gender', 'marital_status', 'rg_issuer', 
                'pis_pasep', 'ctps_number', 'cep', 'city', 'state', 
                'neighborhood', 'address_number', 'department', 
                'contract_type', 'pix_key', 'emergency_contact_name', 
                'emergency_contact_phone'
            ]);
        });
    }
};
