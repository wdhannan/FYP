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
        Schema::create('appointment', function (Blueprint $table) {
            $table->string('AppointmentID', 255)->primary();
            $table->string('ChildID', 255);
            $table->string('DoctorID', 255);
            $table->string('NurseID', 255);
            $table->date('date');
            $table->time('time');
            $table->string('status', 20);
            $table->timestamps();
            
            $table->foreign('ChildID')
                ->references('ChildID')
                ->on('child')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('DoctorID')
                ->references('DoctorID')
                ->on('doctor')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('NurseID')
                ->references('NurseID')
                ->on('nurse')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment');
    }
};
