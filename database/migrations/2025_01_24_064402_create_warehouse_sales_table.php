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
            $table->decimal('total_price', 10, 2);
            $table->decimal('total_discount', 10, 2)->nullable();
            $table->decimal('payable', 10, 2);
            $table->decimal('cur_pay', 10, 2);
            $table->decimal('remained', 10, 2);
            $table->foreignId('currency_id');
            $table->string('note')->nullable();
            $table->string('short_date', 50)->nullable();
            $table->string('ifull_date', 50)->nullable();
            $table->string('iby')->nullable();
            $table->string('uby', 100)->nullable();
            $table->integer('year')->nullable();
            $table->integer('month')->nullable();
            $table->integer('day')->nullable();
            $table->timestamps();

            $table->unique(['billno', 'branch_id'], 'warehouse_sales_billno_branch_unique');
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
