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
            $table->foreignId('account_id');
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('customer_account_id');
            $table->double('total_price');
            $table->double('total_discount')->nullable();
            $table->double('payable');
            $table->double('cur_pay');
            $table->double('remained');
            $table->foreignId('currency_id');
            $table->string('note')->nullable();
            $table->string('short_date', 50)->nullable();
            $table->string('ifull_date', 50)->nullable();
            $table->string('iby')->nullable();
            $table->string('uby', 100)->nullable();
            $table->integer('year')->nullable();
            $table->integer('month')->nullable();
            $table->integer('day')->nullable();
            $table->integer('day')->nullable();
            $table->integer('times')->nullable();
            $table->integer('is_cleared',6)->default(0)->comment('0:not cleared, 1:cleared');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_sales', function (Blueprint $table) {
            $table->dropUnique('warehouse_sales_billno_branch_unique');
        });
    }
};
