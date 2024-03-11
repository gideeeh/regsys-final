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
        Schema::create('appt_mgmt_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('request_limit')->default(10);
            $table->integer('buffer_time_minutes')->default(10);
            $table->time('am_availability_start')->nullable();
            $table->time('am_availability_end')->nullable();
            $table->time('pm_availability_start')->nullable();
            $table->time('pm_availability_end')->nullable();
            $table->json('available_schedules')->nullable();
            $table->text('received_request_reply')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appt_mgmt_settings');
    }
};
