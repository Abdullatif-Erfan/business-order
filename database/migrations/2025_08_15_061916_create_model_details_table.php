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
        Schema::create('model_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->foreignId('model_id');
            $table->foreignId('pre_list_id');
            $table->double('amount');
            $table->integer('unit_id');
            $table->double('price');
            $table->double('total_price');
            $table->foreignId('currency_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_details');
    }
};
