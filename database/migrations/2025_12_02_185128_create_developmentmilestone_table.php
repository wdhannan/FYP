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
        Schema::create('developmentmilestone', function (Blueprint $table) {
            $table->string('MilestoneID', 50)->primary();
            $table->string('ChildID', 50)->nullable();
            $table->string('MilestoneType', 255)->nullable();
            $table->string('Notes', 500)->nullable();
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
        Schema::dropIfExists('developmentmilestone');
    }
};
