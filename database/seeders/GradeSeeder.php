<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('grade_levels')->delete();

        DB::table('grade_levels')->insert([
            ['name' => 'KB', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SD', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
