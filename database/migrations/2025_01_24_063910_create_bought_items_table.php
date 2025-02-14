<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up():void
    {
        Schema::create('bought_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_account_id')->nullable();
            $table->integer('billno')->nullable();
            $table->integer('journal_code');
            $table->decimal('total_price', 10,2);
            $table->decimal('discount', 10,2)->nullable();
            $table->decimal('payable', 10,2)->nullable();
            $table->decimal('cur_pay', 10,2)->nullable();
            $table->decimal('remained', 10,2)->nullable();
            $table->foreignId('account_id'); 
            $table->foreignId('currency_id');
            $table->decimal('trans_spend', 10,2)->nullable();
            $table->foreignId('trans_account_id')->nullable();
            $table->string('note')->nullable();
            $table->string('idate')->nullable();
            $table->integer('year');
            $table->integer('month');
            $table->integer('day');
            $table->string('iby')->nullable();
            $table->string('times')->nullable();
            $table->timestamps();

             // Add unique constraint
             $table->unique(['customer_account_id', 'billno', 'journal_code','branch_id','times'], 'bought_items_unique_constraint');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bought_items');
    }
};
