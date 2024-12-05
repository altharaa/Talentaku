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
            $table->string('semester');
            $table->text('kegiatan_awal_dihalaman');
            $table->string('dihalaman_hasil');
            $table->text('kegiatan_awal_berdoa');
            $table->string('berdoa_hasil');
            $table->text('kegiatan_inti_satu');
            $table->string('inti_satu_hasil');
            $table->text('kegiatan_inti_dua')->nullable();
            $table->string('inti_dua_hasil')->nullable();
            $table->text('kegiatan_inti_tiga')->nullable();
            $table->string('inti_tiga_hasil')->nullable();
            $table->text('snack');
            $table->text('inklusi');
            $table->string('inklusi_hasil');
            $table->string('inklusi_penutup');
            $table->string('inklusi_penutup_hasil');
            $table->text('inklusi_doa');
            $table->string('inklusi_doa_hasil');
            $table->text('catatan');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('student_id');
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
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
