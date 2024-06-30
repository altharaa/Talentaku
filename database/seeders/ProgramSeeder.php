<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('programs')->delete();

        $mockProgramData = [
            [
                'name' => 'Terapi wicara',
                'desc' => 'Program terapi wicara untuk meningkatkan kemampuan bicara anak.',
                'category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pijat stimulasi',
                'desc' => 'Program pijat stimulasi untuk merangsang perkembangan motorik anak.',
                'category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'English Special Club',
                'desc' => 'Club khusus untuk belajar bahasa Inggris dengan cara menyenangkan.',
                'category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Outing Class',
                'desc' => 'Kelas luar ruangan untuk pembelajaran langsung di alam.',
                'category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Catering Sehat',
                'desc' => 'Program catering sehat untuk anak-anak.',
                'category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Musik & Tari',
                'desc' => 'Mengembangkan bakat siswa dalam musik dan tari',
                'category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Kreasi & Gambar',
                'desc' => 'Meningkatkan kreativitas siswa dalam seni visual melalui berbagai teknik gambar dan kerajinan',
                'category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Batik Eco Print',
                'desc' => 'Membuatan batik ramah lingkungan dengan memanfaatkan bahan alami',
                'category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Cooking',
                'desc' => 'Membekali siswa dengan keterampilan memasak dasar melalui praktik langsung di dapur sekolah',
                'category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Renang',
                'desc' => 'Meningkatkan kebugaran fisik dan keterampilan berenang siswa',
                'category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($mockProgramData as $program) {
            DB::table('programs')->insert($program);
        }
    }
}
