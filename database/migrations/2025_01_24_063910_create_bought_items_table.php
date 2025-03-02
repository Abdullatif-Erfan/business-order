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
            $table->integer('billno')->comment('system auto number');
            $table->string('factor')->nullable();
            $table->integer('journal_code')->nullable();
            $table->double('total_price');
            $table->double('discount')->nullable();
            $table->double('payable')->nullable();
            $table->double('cur_pay')->nullable();
            $table->double('remained')->nullable();
            $table->foreignId('account_id'); 
            $table->foreignId('currency_id');
            $table->foreignId('customer_currency_id');
            $table->double('trans_spend')->nullable();
            $table->string('note')->nullable();
            $table->string('idate')->nullable();
            $table->integer('year');
            $table->integer('month');
            $table->integer('day');
            $table->string('iby')->nullable();
            $table->string('times')->nullable();
            $table->integer('is_cleared',6)->default(0)->comment('0:not cleared, 1:cleared');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bought_items');
    }
};
