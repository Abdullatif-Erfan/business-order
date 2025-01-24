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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_currency_id');
            $table->decimal('from_currency_amount', 10, 2)->default(1);
            $table->foreignId('to_currency_id');
            $table->decimal('to_currency_amount', 10, 2);
            $table->decimal('reverse_amount', 10, 2);
            $table->integer('greater_account_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
