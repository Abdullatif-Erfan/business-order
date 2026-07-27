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
       Schema::create('warehouse_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique();
            $table->unsignedBigInteger('warehouse_item_id');
            $table->unsignedBigInteger('pre_list_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('currency_id');
            $table->string('billno')->comment('bought_items bill number');
            $table->date('return_date');
            // Item details
            $table->unsignedBigInteger('supplier_account_id');
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('unit_price_vat', 15, 2)->nullable();
            $table->decimal('tax_percentage', 5, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable();
            $table->decimal('total', 15, 2);
            
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            
            // Return details
            $table->text('reason')->nullable();
            
            // Status: 0=pending, 1=approved, 2=rejected, 3=processed
            $table->tinyInteger('status')->default(0);
            
            // User info
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('billno');
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_returns');
    }
};
