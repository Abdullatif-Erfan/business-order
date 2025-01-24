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
        Schema::create('warehouse_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id');
            $table->integer('buy_pre_id');
            $table->string('name')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->foreignId('unit_id');
            $table->decimal('bought_up', 10, 2)->nullable()->comment('buy unit price');
            $table->decimal('sell_up', 10, 2)->nullable()->comment('selling unit price');
            $table->decimal('total', 10, 2);
            $table->foreignId('currency_id');
            $table->integer('notification_amount')->nullable();
            $table->integer('inserted_by')->nullable()->comment('user_id');
            $table->string('expire_date', 100)->nullable();
            $table->string('inserted_short_date', 30);
            $table->integer('year')->nullable();
            $table->integer('month')->nullable();
            $table->integer('day')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_items');
    }
};
