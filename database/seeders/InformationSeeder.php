<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InformationSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('information')->delete();

        $informationData = [
            [
                'title' => 'Whatsapp',
                'desc' => '0858-7504-8372 Bu Nurul',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Email',
                'desc' => 'yayasanpendidikantumbuhkembang@gmail.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Instagram',
                'desc' => '@kbinklusitalenta\n@sdinklusitalenta\n@rumahbelajar.anak',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Alamat',
                'desc' => 'Jl. HOS Cokroaminoto, Mlati Lor, Gg. Kauman No.187(RT 02 / RW 11) Kudus, Jawa Tengah 59319',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($informationData as $program) {
            DB::table('information')->insert($program);
        }
    }
}
