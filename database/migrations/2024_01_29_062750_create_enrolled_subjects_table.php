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
        Schema::create('enrolled_subjects', function (Blueprint $table) {
            $table->id('en_subjects_id');
            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->float('final_grade')->nullable();
            $table->timestamps();

            $table->foreign('enrollment_id')->references('enrollment_id')->on('enrollments')->onDelete('set null');
            $table->foreign('subject_id')->references('subject_id')->on('subjects')->onDelete('set null');
            $table->foreign('section_id')->references('section_id')->on('sections')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrolled_subjects');
    }
};
