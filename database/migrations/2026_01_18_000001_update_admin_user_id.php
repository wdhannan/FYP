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
        // Check if admin user with UserID '1' exists
        $adminUser = DB::table('user')->where('UserID', '1')->where('role', 'admin')->first();
        
        if ($adminUser) {
            // Check if 'admin' user already exists
            $existingAdmin = DB::table('user')->where('UserID', 'admin')->first();
            
            if (!$existingAdmin) {
                // Drop primary key temporarily to allow update
                Schema::table('user', function (Blueprint $table) {
                    $table->dropPrimary(['UserID']);
                });
                
                // Update the UserID from '1' to 'admin'
                DB::table('user')
                    ->where('UserID', '1')
                    ->where('role', 'admin')
                    ->update(['UserID' => 'admin']);
                
                // Re-add primary key
                Schema::table('user', function (Blueprint $table) {
                    $table->primary('UserID');
                });
            } else {
                // If 'admin' already exists, just delete the old '1' record
                DB::table('user')->where('UserID', '1')->where('role', 'admin')->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if admin user with UserID 'admin' exists
        $adminUser = DB::table('user')->where('UserID', 'admin')->where('role', 'admin')->first();
        
        if ($adminUser) {
            // Check if '1' user already exists
            $existingUser = DB::table('user')->where('UserID', '1')->first();
            
            if (!$existingUser) {
                // Drop primary key temporarily
                Schema::table('user', function (Blueprint $table) {
                    $table->dropPrimary(['UserID']);
                });
                
                // Revert UserID from 'admin' back to '1'
                DB::table('user')
                    ->where('UserID', 'admin')
                    ->where('role', 'admin')
                    ->update(['UserID' => '1']);
                
                // Re-add primary key
                Schema::table('user', function (Blueprint $table) {
                    $table->primary('UserID');
                });
            } else {
                // If '1' already exists, just delete the 'admin' record
                DB::table('user')->where('UserID', 'admin')->where('role', 'admin')->delete();
            }
        }
    }
};
