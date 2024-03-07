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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id('subject_id');
            $table->string('subject_code', 45)->unique();
            $table->string('subject_name', 255);
            $table->text('subject_description')->nullable();
            $table->integer('units_lec');
            $table->integer('units_lab')->default(0);
            $table->unsignedBigInteger('prerequisite_1')->nullable();
            $table->unsignedBigInteger('prerequisite_2')->nullable();
            $table->unsignedBigInteger('prerequisite_3')->nullable();
            $table->timestamps();
            
            // Define the foreign key constraints
            $table->foreign('prerequisite_1')->references('subject_id')->on('subjects')->onDelete('set null');
            $table->foreign('prerequisite_2')->references('subject_id')->on('subjects')->onDelete('set null');
            $table->foreign('prerequisite_3')->references('subject_id')->on('subjects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
