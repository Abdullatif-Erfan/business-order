<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bought_returns', function (Blueprint $table) {
            $table->id();
            
            // Reference to the original bought item
            $table->unsignedBigInteger('bought_item_id')->nullable();
            $table->unsignedBigInteger('bought_item_detail_id')->nullable();
            
            // Return information
            $table->string('billno')->nullable();
            $table->string('return_number')->unique();
            $table->date('return_date');
            
            // Item details
            $table->unsignedBigInteger('supplier_account_id');
            $table->unsignedBigInteger('pre_list_id');
            $table->unsignedBigInteger('unit_id');
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('unit_price_vat', 15, 2)->nullable();
            $table->decimal('total', 15, 2);
            $table->decimal('total_vat', 15, 2)->nullable();
            
            // Tax fields
            $table->decimal('tax_percentage', 5, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable();
            
            // Financial
            $table->unsignedBigInteger('currency_id');
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            
            // Return details
            $table->text('reason')->nullable();
            
            // Status: 0=pending, 1=approved, 2=rejected, 3=processed
            // $table->tinyInteger('status')->default(0);
            
            // User info
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Soft delete
            $table->softDeletes();
            
            // Indexes
            $table->index('bought_item_id');
            $table->index('bought_item_detail_id');
            $table->index('billno');
            // $table->index('return_number');
            // $table->index('supplier_account_id');
            // $table->index('return_date');
            // $table->index('status');
            
            // Foreign keys
            $table->foreign('bought_item_id')->references('id')->on('bought_items')->onDelete('set null');
            $table->foreign('bought_item_detail_id')->references('id')->on('bought_item_details')->onDelete('set null');
            $table->foreign('supplier_account_id')->references('id')->on('accounts');
            $table->foreign('pre_list_id')->references('id')->on('buy_pre_lists');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bought_returns');
    }
};