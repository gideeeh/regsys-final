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
        Schema::create('program_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('term');
            $table->string('year');
            $table->timestamps();

            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('set null');
            $table->foreign('subject_id')->references('subject_id')->on('subjects')->onDelete('set null');

            $table->unique(['program_id', 'subject_id'], 'program_subject_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_subjects');
    }
};
