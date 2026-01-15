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
        Schema::create('birthrecord', function (Blueprint $table) {
            $table->string('BirthID', 50)->primary();
            $table->string('ChildID', 50)->nullable();
            $table->time('TimeOfBirth')->nullable();
            $table->integer('GestationalAgeWeeks')->nullable();
            $table->string('BirthPlace', 255)->nullable();
            $table->string('BirthType', 100)->nullable();
            $table->text('Complications')->nullable();
            $table->integer('BabyCount')->nullable();
            $table->decimal('BirthWeight', 5, 2)->nullable();
            $table->decimal('BirthLength', 5, 2)->nullable();
            $table->decimal('BirthCircumference', 5, 2)->nullable();
            $table->string('VitaminKGiven', 10)->nullable();
            $table->string('ApgarScore', 50)->nullable();
            $table->string('BloodGroup', 10)->nullable();
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
        Schema::dropIfExists('birthrecord');
    }
};
