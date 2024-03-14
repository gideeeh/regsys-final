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
        Schema::create('students', function (Blueprint $table) {
            $table->id('student_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('student_number', 45);
            $table->string('first_name', 45);
            $table->string('middle_name', 45)->nullable();
            $table->string('last_name', 45);
            $table->string('suffix', 10)->nullable();
            $table->string('sex', 10)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('birthplace', 255)->nullable();
            $table->string('civil_status', 45)->nullable();
            $table->string('nationality', 45)->nullable();
            $table->string('religion', 255)->nullable();
            $table->string('phone_number', 255)->nullable();
            $table->string('personal_email', 255)->nullable();
            $table->string('school_email', 255)->nullable();
            $table->string('house_num', 45)->nullable();
            $table->string('street', 255)->nullable();
            $table->string('brgy', 255)->nullable();
            $table->string('city_municipality', 255)->nullable();
            $table->string('province', 255)->nullable();
            $table->string('zipcode', 45)->nullable();
            $table->string('guardian_name', 255)->nullable();
            $table->string('guardian_contact', 45)->nullable();
            $table->string('elementary', 255)->nullable();
            $table->string('elem_yr_grad', 45)->nullable();
            $table->string('jr_highschool', 255)->nullable();
            $table->string('jr_hs_yr_grad', 45)->nullable();
            $table->string('sr_highschool', 255)->nullable();
            $table->string('sr_hs_yr_grad', 45)->nullable();
            $table->string('college', 255)->nullable();
            $table->string('college_year_ended', 45)->nullable();
            $table->boolean('is_transferee')->nullable();
            $table->boolean('is_irregular')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
