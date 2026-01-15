<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table) {
            // Drop primary key first
            $table->dropPrimary(['UserID']);
        });
        
        // Change column type from integer to string
        DB::statement('ALTER TABLE `user` MODIFY `UserID` VARCHAR(50) NOT NULL');
        
        Schema::table('user', function (Blueprint $table) {
            // Re-add primary key
            $table->primary('UserID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            // Drop primary key first
            $table->dropPrimary(['UserID']);
        });
        
        // Change column type back to integer
        DB::statement('ALTER TABLE `user` MODIFY `UserID` INT NOT NULL');
        
        Schema::table('user', function (Blueprint $table) {
            // Re-add primary key
            $table->primary('UserID');
        });
    }
};
