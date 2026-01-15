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
        Schema::create('growthchart', function (Blueprint $table) {
            $table->string('GrowthID', 50)->primary();
            $table->string('ChildID', 50)->nullable();
            $table->date('DateMeasured')->nullable();
            $table->float('Weight')->nullable();
            $table->float('Height')->nullable();
            $table->float('HeadCircumference')->nullable();
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
        Schema::dropIfExists('growthchart');
    }
};
