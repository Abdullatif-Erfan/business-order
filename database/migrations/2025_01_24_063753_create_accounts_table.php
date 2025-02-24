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
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('account_type_id')->index();
            $table->foreignId('branch_id')->index();
            $table->string('name');
            $table->index('name');
            $table->string('phone', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->integer('is_pre_select')->default(0);
            $table->integer('percent')->nullable()->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
