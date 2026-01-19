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
        Schema::table('developmentmilestone', function (Blueprint $table) {
            $table->date('RecordedDate')->nullable()->after('MilestoneType');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('developmentmilestone', function (Blueprint $table) {
            $table->dropColumn('RecordedDate');
        });
    }
};
