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
        Schema::create('student_reports', function (Blueprint $table) {
            $table->id();
            $table->date('created');
            $table->unsignedBigInteger('semester_id');
            $table->text('kegiatan_awal');
            $table->enum('awal_point', ['Muncul', 'Kurang', 'Belum Muncul']);
            $table->text('kegiatan_inti');
            $table->enum('inti_point', ['Muncul', 'Kurang', 'Belum Muncul']);
            $table->text('snack');
            $table->enum('snack_point', ['Muncul', 'Kurang', 'Belum Muncul']);
            $table->text('inklusi');
            $table->enum('inklusi_point', ['Muncul', 'Kurang', 'Belum Muncul']);
            $table->text('catatan');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('grade_id');
            $table->timestamps();

            $table->foreign('semester_id')->references('id')->on('student_report_semesters')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_reports');
    }
};
