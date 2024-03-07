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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('acad_year', 45);
            $table->date('term_1_start')->nullable();
            $table->date('term_1_end')->nullable();
            $table->date('term_2_start')->nullable();
            $table->date('term_2_end')->nullable();
            $table->date('term_3_start')->nullable();
            $table->date('term_3_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
