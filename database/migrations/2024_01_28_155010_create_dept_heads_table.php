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
        Schema::create('dept_heads', function (Blueprint $table) {
            $table->id('dept_head_id');
            $table->string('first_name',50);
            $table->string('middle_name',50)->nullable();
            $table->string('last_name',50);
            $table->string('suffix', 50)->nullable();
            $table->unsignedBigInteger('dept_id')->nullable();;
            $table->string('personal_email', 255)->nullable();
            $table->string('school_email', 255)->nullable();
            $table->timestamps();

            $table->foreign('dept_id')->references('dept_id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dept_heads');
    }
};
