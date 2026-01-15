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
        Schema::create('child', function (Blueprint $table) {
            $table->string('ChildID', 50)->primary();
            $table->string('FullName', 255);
            $table->date('DateOfBirth')->nullable();
            $table->string('Gender', 20)->nullable();
            $table->string('MyKidNumber', 50)->nullable();
            $table->string('ParentID', 50)->nullable();
            $table->string('Ethnic', 100)->nullable();
            $table->timestamps();
            
            $table->foreign('ParentID')
                ->references('ParentID')
                ->on('parent')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child');
    }
};
