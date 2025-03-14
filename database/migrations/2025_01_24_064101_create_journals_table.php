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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->default(0);
            $table->foreignId('account_id');
            $table->integer('bill_no')->nullable()->default(0);
            $table->double('amount');
            $table->foreignId('currency_id');
            $table->integer('transaction_type')->comment('1: recieved, 1 paid');
            $table->integer('payment_type')->comment('1: cache, 2: loan');
            $table->integer('options')->nullable()->comment('1: cache2cache, 2:loan2loan, 3:cache2loan, 4:loan2cache');
            $table->string('option_label')->nullable();
            $table->string('inserted_full_date', 30)->nullable();
            $table->string('inserted_short_date', 30)->nullable();
            $table->string('user')->nullable();
            $table->string('updated_full_date', 30)->nullable();
            $table->integer('year');
            $table->integer('month');
            $table->integer('day');
            $table->string('doc')->nullable();
            $table->string('details')->nullable();
            $table->integer('status')->comment('1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other');
            $table->foreignId('branch_id')->default(0);
            $table->foreignId('dynamic_type')->default(0)->comment('has relation with income_type, expense_type, salary, ....');
            $table->string('dt_comment')->nullable();
            $table->integer('is_middle')->default(0)->comment('0:not, 1:middle record for keeping balance in 3 journal records');

            $table->double('rate')->nullable();
            $table->double('profit')->nullable();
            $table->integer('is_cleared')->comment('0: not cleared, 1:cleared')->default(0);
            $table->integer('cleared_round')->default(0);
            $table->string('times')->default('0');
            $table->integer('is_single_record')->default('0')->comment('0:single, 1:pair');
            $table->integer('belongsToMe')->default('0')->nullable()->comment('0: object 1:subject (my record)');
            $table->timestamps();
        });
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
