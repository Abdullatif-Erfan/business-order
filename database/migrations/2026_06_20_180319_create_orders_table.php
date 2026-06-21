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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('ord_num')->comment('system auto number');
            $table->foreignId('pre_list_id');
            $table->foreignId('category_id');
            $table->foreignId('employee_id')->comment('employee_id = (account_id)');
            $table->foreignId('supplier_id')->comment('supplier_id = (account_id)');
            $table->double('amount');
            $table->foreignId('unit_id');
            $table->string('iby')->comment('Inserted By');
            $table->string('idate')->comment('Inserted Date');
            $table->integer('state')->default(0)->comment('1:new, 2:done');
            $table->integer('done_year')->nullable();
            $table->integer('done_month')->nullable();
            $table->integer('done_day')->nullable();
            $table->string('done_by')->nullable();
            $table->string('times')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
