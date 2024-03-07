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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id('enrollment_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->string('academic_year', 45);
            $table->string('term', 45);
            $table->string('year_level', 45);
            $table->string('batch', 45)->nullable();
            $table->date('enrollment_date');
            $table->enum('scholarship_type', ['none', 'academic', 'working', 'government'])->nullable()->default('none');
            $table->enum('status', ['pending', 'cancelled', 'rejected', 'success'])->nullable()->default('pending');
            $table->enum('enrollment_method',['continuing','new','transferee','shiftee'])->nullable()->default('continuing');
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('set null');
            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
