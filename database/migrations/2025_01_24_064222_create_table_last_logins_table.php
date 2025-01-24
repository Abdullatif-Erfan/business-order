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
        Schema::create('table_last_logins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('userId');
            $table->string('sessionData');
            $table->string('machineIp');
            $table->string('userAgent');
            $table->string('agentString');
            $table->string('platform');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_last_logins');
    }
};
