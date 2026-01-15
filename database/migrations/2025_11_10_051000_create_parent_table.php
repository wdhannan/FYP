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
        Schema::create('parent', function (Blueprint $table) {
            $table->string('ParentID', 50)->primary();
            $table->string('MotherName', 255);
            $table->string('MphoneNumber', 20)->nullable();
            $table->string('MEmail', 255)->nullable();
            $table->string('MIdentificationNumber', 50)->nullable();
            $table->string('FatherName', 255)->nullable();
            $table->string('FPhoneNumber', 20)->nullable();
            $table->string('FEmail', 255)->nullable();
            $table->string('FIdentificationNumber', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent');
    }
};
