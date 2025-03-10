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
            $table->double('in_amount')->nullable();
            $table->double('out_amount')->nullable();
            $table->double('available_amount')->nullable();
            $table->double('wastage_amount')->nullable();
            $table->double('wastage_total')->nullable();
            $table->foreignId('unit_id');
            $table->foreignId('branch_id');
            $table->double('bought_up')->nullable()->comment('buy unit price');
            $table->double('avg_up')->nullable()->comment('average unit price');
            $table->double('sell_up')->nullable()->comment('selling unit price');
            $table->double('total')->comment('transfered total');
            $table->double('available_total')->comment('Available total');
            $table->foreignId('currency_id');
            $table->integer('notification_amount')->nullable();
            $table->string('inserted_by')->nullable()->comment('user_name'); // Fixed varchar issue
            $table->string('expire_date', 100)->nullable();
            $table->string('inserted_short_date', 30);
            $table->integer('year')->nullable();
            $table->integer('month')->nullable();
            $table->integer('day')->nullable();
            $table->integer('times')->nullable();
            $table->integer('is_cleared',6)->default(0)->comment('0:not cleared, 1:cleared');
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
