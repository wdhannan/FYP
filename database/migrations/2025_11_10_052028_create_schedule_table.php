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
        Schema::create('schedule', function (Blueprint $table) {
            $table->string('ScheduleID', 50)->primary();
            $table->string('DoctorID', 50)->nullable();
            $table->date('UploadDate')->nullable();
            $table->string('FileName', 255)->nullable();
            $table->timestamps();
            
            $table->foreign('DoctorID')
                ->references('DoctorID')
                ->on('doctor')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule');
    }
};
