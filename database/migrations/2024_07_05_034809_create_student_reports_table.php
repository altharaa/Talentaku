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
            $table->text('kegiatan_awal_dihalaman');
            $table->enum('dihalaman_hasil', ['Muncul', 'Kurang', 'Belum Muncul']);
            $table->text('kegiatan_awal_berdoa');
            $table->enum('berdoa_hasil', ['Muncul', 'Kurang', 'Belum Muncul']);
            $table->text('kegiatan_inti_satu');
            $table->enum('inti_satu_hasil', ['Muncul', 'Kurang', 'Belum Muncul']);
            $table->text('kegiatan_inti_dua')->nullable();
            $table->enum('inti_dua_hasil', ['Muncul', 'Kurang', 'Belum Muncul'])->nullable();
            $table->text('kegiatan_inti_tiga')->nullable();
            $table->enum('inti_tiga_hasil', ['Muncul', 'Kurang', 'Belum Muncul'])->nullable();
            $table->text('snack');
            $table->text('inklusi');
            $table->enum('inklusi_hasil', ['Muncul', 'Kurang', 'Belum Muncul']);
            $table->enum('inklusi_penutup', ['Menyanyi', 'Ulasan', 'Icebreak']);
            $table->enum('inklusi_penutup_hasil', ['Muncul', 'Kurang', 'Belum Muncul']);
            $table->text('inklusi_doa');
            $table->enum('inklusi_doa_hasil', ['Muncul', 'Kurang', 'Belum Muncul']);
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
