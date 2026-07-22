<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::create('sales_bill_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_sales_id');
            $table->string('billno');
            $table->unsignedBigInteger('customer_account_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('currency_id');
            $table->decimal('amount', 15, 2);
            $table->decimal('remaining_after_payment', 15, 2);
            // $table->enum('payment_type', ['cash', 'bank_transfer', 'cheque', 'talabat'])->default('cash');
            $table->date('payment_date');
            $table->text('note')->nullable();
            $table->string('journal_code')->nullable(); 
            $table->unsignedBigInteger('user_id');
            $table->string('user_name')->nullable();
            $table->bigInteger('times')->nullable();
            $table->timestamps();

            // Foreign keys
            // $table->foreign('warehouse_sales_id')->references('id')->on('warehouse_sales')->onDelete('cascade');
            // $table->foreign('customer_account_id')->references('id')->on('accounts');
            // $table->foreign('account_id')->references('id')->on('accounts');
            // $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales_bill_payments');
    }
};
