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
        Schema::create('account_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('is_disabled')->nullable()->default(0)->comment('0:not disabled, 1:disabled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_types');
    }
};
