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
        Schema::create('clearances', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['buy', 'sell'])->default('buy');  // Add the enum column
            $table->foreignId('company_account_id');
            $table->foreignId('customer_account_id');
            $table->double('total');
            $table->foreignId('currency_id');
            $table->foreignId('branch_id');
            $table->string('details');
            $table->json('bill_numbers');
            $table->string('dates');
            $table->string('clearedBy'); 
            $table->timestamps();
        });

       

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clearances');
    }

};
