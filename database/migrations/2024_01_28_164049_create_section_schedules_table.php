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
        Schema::create('section_schedules', function (Blueprint $table) {
            $table->id('section_sched_id');
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('prof_id')->nullable();
            $table->string('class_day', 45);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 45)->nullable();
            $table->timestamps();

            $table->foreign('section_id')->references('section_id')->on('sections')->onDelete('set null');
            $table->foreign('prof_id')->references('prof_id')->on('professors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_schedules');
    }
};
