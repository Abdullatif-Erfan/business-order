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
        Schema::create('draft_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->comment('customer_id = (account_id)');
            $table->foreignId('pre_list_id');
            $table->double('amount');
            $table->foreignId('unit_id');
            $table->string('iby')->comment('Inserted By');
            $table->string('idate')->comment('Inserted Date');
            $table->string('user_name')->nullable();
            $table->integer('state')->default(1)->comment('1:new, 2:progress, 3:done');
            $table->string('times')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draft_orders');
    }
};
