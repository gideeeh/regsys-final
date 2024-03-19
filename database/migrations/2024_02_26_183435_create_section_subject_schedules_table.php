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
        Schema::create('section_subject_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sec_sub_id')->nullable();
            $table->unsignedBigInteger('prof_id')->nullable();
            $table->json('class_days_f2f')->nullable();
            $table->json('class_days_online')->nullable();
            $table->time('start_time_f2f')->nullable();
            $table->time('end_time_f2f')->nullable();
            $table->time('start_time_online')->nullable();
            $table->time('end_time_online')->nullable();
            $table->string('room')->nullable();
            $table->integer('class_limit')->nullable();
            $table->timestamps();

            $table->foreign('sec_sub_id')->references('id')->on('section_subjects')->onDelete('set null');
            $table->foreign('prof_id')->references('prof_id')->on('professors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_subject_schedules');
    }
};
