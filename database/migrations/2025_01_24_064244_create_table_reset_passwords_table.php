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
        Schema::create('table_reset_passwords', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email');
            $table->string('activation_id');
            $table->string('agent');
            $table->string('client_ip');
            $table->tinyInteger('isDeleted')->default(0);
            $table->bigInteger('createdBy')->default(1);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_reset_passwords');
    }
};
