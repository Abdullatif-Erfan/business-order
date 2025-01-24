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
        Schema::create('table_access_metrics', function (Blueprint $table) {
            $table->id();
            $table->text('access');
            $table->integer('roleId');
            $table->integer('isDeleted')->default(0);
            $table->integer('createdBy');
            $table->dateTime('createdDtm');
            $table->integer('updatedBy')->nullable();
            $table->dateTime('updatedDtm')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_access_metrics');
    }
};
