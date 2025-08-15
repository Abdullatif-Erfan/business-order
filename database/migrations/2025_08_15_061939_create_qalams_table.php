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
        Schema::create('qalams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->foreignId('model_id');
            $table->double('amount');
            $table->integer('unit_id');
            $table->double('unit_price');
            $table->double('total_price');
            $table->foreignId('currency_id');
            $table->string('dates');
            $table->string('user',100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qalams');
    }
};
