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
        Schema::create('report', function (Blueprint $table) {
            $table->string('ReportID', 50)->primary();
            $table->string('ChildID', 50)->nullable();
            $table->string('DoctorID', 50)->nullable();
            $table->date('ReportDate')->nullable();
            $table->text('Diagnosis')->nullable();
            $table->text('Symptoms')->nullable();
            $table->text('Findings')->nullable();
            $table->text('FollowUpAdvices')->nullable();
            $table->text('Notes')->nullable();
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report');
    }
};
