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
        Schema::table('sections', function (Blueprint $table) {
            $table->string('year_level', 20)->nullable();
            
            // Correctly drop the foreign key using the constraint name
            $table->dropForeign(['subject_id']); // The array should contain the column name

            $table->dropColumn('subject_id');
            $table->dropColumn('is_openedUponRequest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable(); 
            $table->boolean('is_openedUponRequest')->default(false);

            // Add the foreign key constraint back
            $table->foreign('subject_id')->references('subject_id')->on('subjects')->onDelete('set null');

            // Remove the 'year_level' column last
            $table->dropColumn('year_level');
        });
    }

};
