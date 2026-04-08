<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add Role to Users
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('CASHIER')->after('password');
        });

        // Digital Signage Medias
        Schema::create('standby_medias', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // IMAGE, VIDEO
            $table->string('file_path');
            $table->integer('sort_order')->default(0);
            $table->integer('duration_seconds')->default(10); // How long slide stays, if video, it can be video length
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        Schema::dropIfExists('standby_medias');
    }
};
