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
        ];

        foreach ($mockProgramData as $program) {
            DB::table('programs')->insert($program);
        }
    }
}
