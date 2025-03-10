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
        Schema::table('enrolled_subjects', function (Blueprint $table) {
            $table->string('enrolledSubject_code',255)->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrolled_subjects', function (Blueprint $table) {
            $table->dropColumn('enrolledSubject_code');
        });
    }
};
