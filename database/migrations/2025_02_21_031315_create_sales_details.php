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
        Schema::create('sales_details', function (Blueprint $table) {
            $table->id();
            $table->integer('billno')->nullable();
            $table->foreignId('branch_id');
            $table->foreignId('warehouse_id');
            $table->foreignId('warehouse_sales_id');
            $table->foreignId('pre_list_id')->nullable();
            $table->integer('unit_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('avg_up', 10, 2)->nullable();
            $table->decimal('sell_up', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('profit', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->integer('is_returned')->default(0)->comment('0:not returned, 1:returned');
            $table->string('todays_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_details');
    }
};
