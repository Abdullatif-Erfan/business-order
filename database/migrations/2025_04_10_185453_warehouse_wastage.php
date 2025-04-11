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
        Schema::create('warehouse_wastage', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('warehouse_id');
            $table->foreignId('buy_pre_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('bought_up', 10, 2);
            $table->decimal('total', 10, 2);
            $table->foreignId('unit_id');
            $table->foreignId('currency_id');
            $table->foreignId('branch_id');
            $table->integer('year');
            $table->integer('month');
            $table->integer('day');
            $table->string('idate')->nullable();
            $table->string('iby')->nullable();
            $table->string('expired_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_wastage');
    }
};
