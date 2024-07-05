<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('student_report_semesters')->delete();

        DB::table('student_report_semesters')->insert([
            ['id' => 1, 'name' => 'Semester 1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Semester 2', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('student_report_points')->delete();

        DB::table('student_report_points')->insert([
            ['id' => 1, 'name' => 'Muncul', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Kurang', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Belum Muncul', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
