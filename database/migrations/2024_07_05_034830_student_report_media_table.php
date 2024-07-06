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
        Schema::create('student_report_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_report_id');
            $table->string('file_path');
            $table->timestamps();

            $table->foreign('student_report_id')->references('id')->on('student_reports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_report_media');
    }
};
