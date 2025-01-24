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
        Schema::create('bought_item_details', function (Blueprint $table) {
            $table->id(); // Automatically adds an auto-incrementing primary key 'id'
            $table->integer('billno')->nullable();
            $table->foreignId('bought_item_id');
            $table->foreignId('pre_list_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('bought_up', 10, 2)->nullable();
            $table->decimal('sell_up', 10, 2)->nullable();
            $table->integer('unit_id')->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->decimal('commission', 10, 2)->nullable();
            $table->decimal('total_commission', 10, 2)->nullable();
            $table->decimal('total_transport', 10, 2)->nullable();
            $table->decimal('payable', 10, 2)->nullable();
            $table->integer('is_moved'); // 'is_moved' is not a primary key
            $table->string('expire_date')->nullable();
            $table->string('times');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bought_item_details');
    }
};
