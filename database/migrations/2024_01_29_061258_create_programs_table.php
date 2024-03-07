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
        Schema::create('programs', function (Blueprint $table) {
            $table->id('program_id');
            $table->string('program_code',50)->unique();
            $table->string('program_name',255);
            $table->text('program_desc')->nullable();
            $table->string('degree_type',255);
            $table->unsignedBigInteger('dept_id')->nullable();
            $table->string('program_coordinator',255)->nullable();
            $table->integer('total_units')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('dept_id')->references('dept_id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
