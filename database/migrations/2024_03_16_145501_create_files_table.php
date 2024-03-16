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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_extension')->nullable();
            $table->dateTime('uploaded_at')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable(); 
            $table->timestamps();
        
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('set null');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
        });   
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
