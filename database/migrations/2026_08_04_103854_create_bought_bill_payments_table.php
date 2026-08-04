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
        Schema::create('bought_bill_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bought_item_id');
            $table->string('billno');
            $table->unsignedBigInteger('supplier_account_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('currency_id');
            $table->decimal('cur_pay', 15, 2);
            $table->decimal('remained', 15, 2)->default(0);
            $table->date('payment_date');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('journal_code')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->bigInteger('times')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('billno');
            $table->index('payment_date');
            $table->index('supplier_account_id');
            $table->index('account_id');

            // Foreign Keys
            $table->foreign('bought_item_id')
                  ->references('id')
                  ->on('bought_items')
                  ->onDelete('cascade');
            
            $table->foreign('supplier_account_id')
                  ->references('id')
                  ->on('accounts');
            
            $table->foreign('account_id')
                  ->references('id')
                  ->on('accounts');
            
            $table->foreign('currency_id')
                  ->references('id')
                  ->on('currencies');
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bought_bill_payments');
    }
};
