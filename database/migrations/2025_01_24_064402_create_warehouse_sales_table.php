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
        Schema::create('warehouse_sales', function (Blueprint $table) {
            $table->id();
            $table->integer('billno')->nullable();
            $table->string('factor')->nullable();
            $table->foreignId('warehouse_item_id');
            $table->foreignId('account_id');
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('customer_account_id');
            $table->string('item_name');
            $table->foreignId('unit_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('sell_up', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('profit', 10, 2)->nullable();
            $table->decimal('total', 10, 2);
            $table->decimal('general_discount', 10, 2)->nullable();
            $table->decimal('payable', 10, 2);
            $table->decimal('cur_pay', 10, 2);
            $table->decimal('remained', 10, 2);
            $table->foreignId('currency_id');
            $table->decimal('total_price', 10, 2);
            $table->string('note')->nullable();
            $table->string('ifull_date', 50)->nullable();
            $table->integer('iby')->nullable();
            $table->string('uby', 30)->nullable();
            $table->integer('year')->nullable();
            $table->integer('month')->nullable();
            $table->integer('day')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_sales');
    }
};
