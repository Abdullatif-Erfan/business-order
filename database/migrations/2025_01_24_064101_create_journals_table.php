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
            $table->integer('code');
            $table->foreignId('account_id');
            $table->integer('bill_no')->default(0);
            $table->decimal('amount', 10, 2);
            $table->foreignId('currency');
            $table->integer('transaction_type')->comment('1: recieved, 1 paid');
            $table->integer('payment_type')->comment('1: cache, 2: loan');
            $table->string('inserted_full_date', 30)->nullable();
            $table->string('inserted_short_date', 30)->nullable();
            $table->foreignId('user_id');
            $table->string('updated_full_date', 30)->nullable();
            $table->integer('year');
            $table->integer('month');
            $table->integer('day');
            $table->string('doc')->nullable();
            $table->string('details')->nullable();
            $table->integer('status')->comment('1: old journal, 2: journal, 3:buy, 4:sales, 5:clearance');
            $table->foreignId('branch_id')->nullable()->default(0);
            $table->decimal('rate', 10, 2)->nullable();
            $table->decimal('profit', 10, 2)->nullable();
            $table->integer('is_cleared')->comment('0: not cleared, 1:cleared')->default(0);
            $table->integer('cleared_round')->default(0);
            $table->string('times')->default('0');
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
