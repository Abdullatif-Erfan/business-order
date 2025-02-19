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
            $table->decimal('in_amount', 10, 2)->nullable();
            $table->decimal('out_amount', 10, 2)->nullable();
            $table->decimal('available_amount', 10, 2)->nullable();
            $table->decimal('wastage_amount', 10, 2)->nullable();
            $table->decimal('wastage_total', 10, 2)->nullable();
            $table->foreignId('unit_id');
            $table->decimal('bought_up', 10, 2)->nullable()->comment('buy unit price');
            $table->decimal('avg_up', 10, 2)->nullable()->comment('average unit price');
            $table->decimal('sell_up', 10, 2)->nullable()->comment('selling unit price');
            $table->decimal('total', 10, 2)->comment('transfered total');
            $table->decimal('available_total', 10, 2)->comment('Available total');
            $table->foreignId('currency_id');
            $table->integer('notification_amount')->nullable();
            $table->string('inserted_by')->nullable()->comment('user_name'); // Fixed varchar issue
            $table->string('expire_date', 100)->nullable();
            $table->string('inserted_short_date', 30);
            $table->integer('year')->nullable();
            $table->integer('month')->nullable();
            $table->integer('day')->nullable();
            $table->integer('times')->nullable();
            $table->timestamps();
            
            // Add unique constraint
            // $table->unique(['warehouse_id', 'buy_pre_id', 'unit_id', 'times'], 'warehouse_items_unique_constraint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_items'); // Properly rollback the table
    }
};
