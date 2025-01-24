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
        Schema::create('table_users', function (Blueprint $table) {
            $table->increments('userId');
            $table->string('username', 100);
            $table->string('email', 128);
            $table->string('password', 128);
            $table->string('name', 128)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->tinyInteger('roleId');
            $table->tinyInteger('isAdmin')->default(2);
            $table->tinyInteger('isDeleted')->default(0);
            $table->integer('isHidden')->default(0);
            $table->string('photo', 255)->nullable();
            $table->integer('createdBy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_users');
    }
};
