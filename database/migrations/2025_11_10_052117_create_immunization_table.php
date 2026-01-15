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
        Schema::create('immunization', function (Blueprint $table) {
            $table->string('ImmunizationID', 50)->primary();
            $table->string('ChildID', 50)->nullable();
            $table->integer('Age')->nullable();
            $table->string('VaccineName', 255)->nullable();
            $table->date('Date')->nullable();
            $table->string('DoseNumber', 50)->nullable();
            $table->string('GivenBy', 255)->nullable();
            $table->timestamps();
            
            $table->foreign('ChildID')
                ->references('ChildID')
                ->on('child')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immunization');
    }
};
