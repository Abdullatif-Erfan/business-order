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
        // Create migration for buy_invoices
        Schema::create('buy_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedInteger('supplier_id');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0:draft, 1:pending, 2:partial, 3:paid, 4:cancelled');
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('times')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('accounts');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });

        // Create migration for buy_invoice_items
        Schema::create('buy_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('bought_item_detail_id')->nullable();
            $table->unsignedBigInteger('bought_item_id')->nullable();
            $table->unsignedBigInteger('pre_list_id')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('times')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('buy_invoices')->onDelete('cascade');
            $table->foreign('pre_list_id')->references('id')->on('bought_item_pre_lists');
        });

        // Create migration for buy_invoice_payments
        Schema::create('buy_invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->date('payment_date');
            $table->decimal('amount', 15, 2)->default(0);
            $table->tinyInteger('payment_method')->default(1)->comment('1:cash, 2:bank, 3:loan');
            $table->unsignedInteger('account_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('times')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('buy_invoices')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buy_invoices');
    }
};
