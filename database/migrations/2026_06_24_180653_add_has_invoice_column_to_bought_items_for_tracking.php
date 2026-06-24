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
        Schema::table('bought_items', function (Blueprint $table) {
            $table->boolean('has_invoice')->default(0)->after('times');
            $table->unsignedBigInteger('invoice_id')->nullable()->after('has_invoice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bought_items_for_tracking', function (Blueprint $table) {
            //
        });
    }
};
