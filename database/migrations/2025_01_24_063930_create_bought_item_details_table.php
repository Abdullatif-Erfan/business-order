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
        Schema::create('bought_item_details', function (Blueprint $table) {
            $table->id(); // Automatically adds an auto-incrementing primary key 'id'
            $table->integer('billno')->nullable();
            $table->foreignId('branch_id');
            $table->foreignId('bought_item_id');
            $table->foreignId('pre_list_id')->nullable();
            $table->foreignId('customer_account_id')->nullable();
            $table->double('amount');
            $table->double('bought_up')->nullable();
            $table->double('sell_up')->nullable();
            $table->integer('unit_id')->nullable();
            $table->double('discount')->nullable();
            $table->double('transport')->nullable();
            $table->double('total')->nullable();
            $table->integer('is_moved')->default(0)->comment('0:not moved, 1:moved');
            $table->string('expire_date')->nullable();
            $table->string('times');
            $table->timestamps();

            // Add unique constraint
            // $table->unique(['billno', 'bought_item_id', 'pre_list_id','times'], 'bought_item_details_unique_constraint');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bought_item_details');
    }
};
